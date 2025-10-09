<?php

namespace App\Http\Controllers;

use App\Models\SkpiData;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;

class DriveVerificationController extends Controller
{
    /**
     * Verify the contents of the Google Drive folder against the student's data
     */
    public function verifyDriveContents(Request $request, $skpiId)
    {
        $skpi = SkpiData::with(['user', 'jurusan'])->findOrFail($skpiId);

        // Validate that the user has permission to access this
        // For admin users, we can skip the user ownership check
        // If you want to restrict by user, uncomment the next line:
        // $request->user()->can('view', $skpi);

        if (!$skpi->drive_link) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada link Google Drive yang disediakan'
            ]);
        }

        try {
            // Extract folder ID from Google Drive URL
            $folderId = $this->extractFolderId($skpi->drive_link);
            if (!$folderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengekstrak ID folder dari URL Google Drive'
                ]);
            }

            // Use Google Drive API to get the actual contents
            $results = $this->verifyDriveContentsWithAPI($skpi, $folderId);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Extract folder ID from Google Drive URL
     */
    private function extractFolderId($url)
    {
        // Match both folder URLs and direct URLs
        $pattern = '/(?:folders\/|id=|\/d\/)([a-zA-Z0-9_-]+)/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Verify drive contents using Google Drive API with recursive folder scanning and authenticity verification
     */
    private function verifyDriveContentsWithAPI($skpi, $folderId)
    {
        // Initialize Google Client
        $client = new Client();
        
        // Use Service Account credentials
        $serviceAccountFile = storage_path('app/' . env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'));
        
        if (!file_exists($serviceAccountFile)) {
            throw new \Exception('File credentials Google Drive tidak ditemukan. Silakan atur GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE di .env');
        }
        
        $client->setAuthConfig($serviceAccountFile);
        $client->addScope(Drive::DRIVE_READONLY); // Only read access needed
        
        $driveService = new Drive($client);

        // Recursively get all files in the folder and subfolders
        $allFiles = $this->getAllFilesRecursively($driveService, $folderId);
        
        $driveFileDetails = [];
        foreach ($allFiles as $file) {
            $driveFileDetails[] = [
                'name' => strtolower($file->getName()),
                'id' => $file->getId(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize() ?? null, // Handle null size for Google native files
                'modifiedTime' => $file->getModifiedTime(),
                'parents' => $file->getParents()
            ];
        }

        // Prepare student's achievements
        $achievements = [
            ['type' => 'prestasi_akademik', 'content' => $skpi->prestasi_akademik, 'label' => 'Prestasi Akademik'],
            ['type' => 'prestasi_non_akademik', 'content' => $skpi->prestasi_non_akademik, 'label' => 'Prestasi Non-Akademik'],
            ['type' => 'organisasi', 'content' => $skpi->organisasi, 'label' => 'Pengalaman Organisasi'],
            ['type' => 'pengalaman_kerja', 'content' => $skpi->pengalaman_kerja, 'label' => 'Pengalaman Kerja/Magang'],
            ['type' => 'sertifikat_kompetensi', 'content' => $skpi->sertifikat_kompetensi, 'label' => 'Sertifikat Kompetensi']
        ];

        $results = [
            'folder_id' => $folderId,
            'folder_url' => $skpi->drive_link,
            'total_achievements' => 0,
            'total_documents_in_drive' => count($driveFileDetails),
            'total_files_scanned' => count($driveFileDetails),
            'matched_items' => [],
            'missing_items' => [],
            'extra_items' => [],
            'authenticity_checks' => [],
            'status' => 'complete',
            'scan_summary' => [
                'pdf_count' => 0,
                'image_count' => 0,
                'document_count' => 0
            ]
        ];

        // Categorize files by type for summary
        foreach ($driveFileDetails as $file) {
            if (strpos($file['mimeType'], 'pdf') !== false) {
                $results['scan_summary']['pdf_count']++;
            } elseif (strpos($file['mimeType'], 'image') !== false) {
                $results['scan_summary']['image_count']++;
            } elseif (strpos($file['mimeType'], 'document') !== false || strpos($file['mimeType'], 'sheet') !== false || strpos($file['mimeType'], 'presentation') !== false) {
                $results['scan_summary']['document_count']++;
            }
        }

        // Track files that have been matched to avoid duplicates
        $matchedFileIds = [];
        
        // Process each achievement
        foreach ($achievements as $achievement) {
            if (!empty($achievement['content'])) {
                $results['total_achievements']++;
                
                // Look for matching documents in drive (with better search logic)
                $potentialMatches = $this->findPotentialMatches($achievement, $driveFileDetails);
                
                if (!empty($potentialMatches)) {
                    // Take only the best match for this achievement to avoid duplicates
                    $bestMatch = $potentialMatches[0];
                    
                    if (!in_array($bestMatch['id'], $matchedFileIds)) {
                        $results['matched_items'][] = [
                            'type' => $achievement['label'],
                            'content' => $achievement['content'],
                            'file_name' => $bestMatch['name'],
                            'file_id' => $bestMatch['id'],
                            'similarity_score' => $bestMatch['similarity_score']
                        ];
                        
                        $matchedFileIds[] = $bestMatch['id'];
                        
                        // Perform authenticity check
                        $authenticity = $this->checkFileAuthenticity($bestMatch, $achievement, $driveService);
                        $results['authenticity_checks'][] = $authenticity;
                    } else {
                        // If the best match was already used for another achievement, find another match
                        $foundAlternative = false;
                        foreach ($potentialMatches as $match) {
                            if (!in_array($match['id'], $matchedFileIds)) {
                                $results['matched_items'][] = [
                                    'type' => $achievement['label'],
                                    'content' => $achievement['content'],
                                    'file_name' => $match['name'],
                                    'file_id' => $match['id'],
                                    'similarity_score' => $match['similarity_score']
                                ];
                                
                                $matchedFileIds[] = $match['id'];
                                
                                // Perform authenticity check
                                $authenticity = $this->checkFileAuthenticity($match, $achievement, $driveService);
                                $results['authenticity_checks'][] = $authenticity;
                                
                                $foundAlternative = true;
                                break;
                            }
                        }
                        
                        if (!$foundAlternative) {
                            $results['missing_items'][] = [
                                'type' => $achievement['label'],
                                'content' => $achievement['content'],
                                'expected_file' => $this->generateExpectedFileName($achievement['label'], $achievement['content'])
                            ];
                        }
                    }
                } else {
                    $results['missing_items'][] = [
                        'type' => $achievement['label'],
                        'content' => $achievement['content'],
                        'expected_file' => $this->generateExpectedFileName($achievement['label'], $achievement['content'])
                    ];
                }
            }
        }

        // Find extra items (files without matching achievement)
        $matchedFileIds = [];
        foreach ($results['matched_items'] as $matchedItem) {
            $matchedFileIds[] = $matchedItem['file_id'];
        }
        
        foreach ($driveFileDetails as $fileDetail) {
            if (!in_array($fileDetail['id'], $matchedFileIds)) {
                $results['extra_items'][] = [
                    'file_name' => $fileDetail['name'],
                    'id' => $fileDetail['id'],
                    'mimeType' => $fileDetail['mimeType'],
                    'size' => $fileDetail['size'] ?? 0
                ];
            }
        }
        
        // Calculate overall validation score
        $validationScore = $this->calculateValidationScore($results);
        $results['validation_score'] = $validationScore;
        
        return $results;
    }
    
    /**
     * Calculate overall validation score based on matches, authenticity, and completeness
     */
    private function calculateValidationScore($results)
    {
        $totalAchievements = $results['total_achievements'];
        $matchedCount = count($results['matched_items']);
        $authenticityChecks = $results['authenticity_checks'];
        
        if ($totalAchievements === 0) {
            return [
                'percentage' => 100,
                'message' => 'Tidak ada data prestasi untuk divalidasi',
                'status' => 'info'
            ];
        }
        
        // Base score from matches
        $baseScore = ($matchedCount / $totalAchievements) * 60; // 60% of total score
        
        // Authenticity score
        $authenticityScore = 0;
        if (!empty($authenticityChecks)) {
            $highConfidenceCount = 0;
            $mediumConfidenceCount = 0;
            
            foreach ($authenticityChecks as $check) {
                if ($check['confidence_level'] === 'high') {
                    $highConfidenceCount++;
                } elseif ($check['confidence_level'] === 'medium') {
                    $mediumConfidenceCount++;
                }
            }
            
            $authenticityPoints = (($highConfidenceCount * 1.0 + $mediumConfidenceCount * 0.5) / count($authenticityChecks)) * 40; // 40% of total score
            $authenticityScore = $authenticityPoints;
        }
        
        $overallScore = min(100, $baseScore + $authenticityScore); // Cap at 100%
        
        // Determine message and status
        $message = '';
        $status = '';
        
        if ($overallScore >= 85) {
            $message = 'Dokumen sangat lengkap dan sesuai';
            $status = 'excellent';
        } elseif ($overallScore >= 70) {
            $message = 'Dokumen cukup lengkap dan sebagian besar sesuai';
            $status = 'good';
        } elseif ($overallScore >= 50) {
            $message = 'Dokumen cukup lengkap tetapi ada beberapa ketidaksesuaian';
            $status = 'average';
        } elseif ($overallScore >= 30) {
            $message = 'Dokumen kurang lengkap, perlu penambahan';
            $status = 'below_average';
        } else {
            $message = 'Dokumen tidak lengkap, banyak data tanpa bukti pendukung';
            $status = 'poor';
        }
        
        $missingCount = count($results['missing_items']);
        if ($missingCount > 0 && $overallScore < 100) {
            $message .= " - Terdapat {$missingCount} item yang belum memiliki bukti pendukung.";
        }
        
        // Add extra items info
        $extraCount = count($results['extra_items']);
        if ($extraCount > 0) {
            $message .= " - Terdapat {$extraCount} file tambahan yang tidak terkait langsung.";
        }
        
        return [
            'percentage' => round($overallScore, 2),
            'message' => $message,
            'status' => $status
        ];
    }

    /**
     * Recursively get all files from a folder and its subfolders
     */
    private function getAllFilesRecursively($driveService, $folderId, &$allFiles = [])
    {
        $pageToken = null;
        
        do {
            $optParams = [
                'q' => sprintf("'%s' in parents and mimeType != 'application/vnd.google-apps.folder'", $folderId),
                'fields' => 'nextPageToken, files(id, name, mimeType, size, modifiedTime, parents)',
                'pageToken' => $pageToken,
                'pageSize' => 1000
            ];
            
            $files = $driveService->files->listFiles($optParams);
            foreach ($files as $file) {
                $allFiles[] = $file;
            }
            
            $pageToken = $files->getNextPageToken();
        } while ($pageToken != null);
        
        // Now get subfolders and recursively scan them
        $pageToken = null;
        
        do {
            $optParams = [
                'q' => sprintf("'%s' in parents and mimeType = 'application/vnd.google-apps.folder'", $folderId),
                'fields' => 'nextPageToken, files(id, name, mimeType)',
                'pageToken' => $pageToken,
                'pageSize' => 1000
            ];
            
            $folders = $driveService->files->listFiles($optParams);
            
            foreach ($folders as $folder) {
                $this->getAllFilesRecursively($driveService, $folder->getId(), $allFiles);
            }
            
            $pageToken = $folders->getNextPageToken();
        } while ($pageToken != null);
        
        return $allFiles;
    }

    /**
     * Find potential matching files for an achievement
     */
    private function findPotentialMatches($achievement, $driveFileDetails)
    {
        $potentialMatches = [];
        $contentLower = strtolower($achievement['content']);
        $labelLower = strtolower($achievement['label']);
        
        foreach ($driveFileDetails as $fileDetail) {
            $fileName = $fileDetail['name'];
            $similarityScore = 0;
            
            // Check similarity with achievement content
            similar_text($fileName, $contentLower, $contentSimilarity);
            // Check similarity with achievement label
            similar_text($fileName, $labelLower, $labelSimilarity);
            
            // Also check if any word from achievement content is in filename
            $contentWords = explode(' ', $contentLower);
            $wordMatches = 0;
            foreach ($contentWords as $word) {
                if (strlen($word) > 2 && strpos($fileName, $word) !== false) { // Only check words longer than 2 chars
                    $wordMatches++;
                }
            }
            
            $similarityScore = max($contentSimilarity, $labelSimilarity) + ($wordMatches * 10);
            
            // If similarity is high enough, consider it a match
            if ($similarityScore > 30) { // Threshold for matching
                $potentialMatches[] = [
                    'name' => $fileDetail['name'],
                    'id' => $fileDetail['id'],
                    'mimeType' => $fileDetail['mimeType'],
                    'similarity_score' => $similarityScore
                ];
            }
        }
        
        // Sort by similarity score descending
        usort($potentialMatches, function($a, $b) {
            return $b['similarity_score'] - $a['similarity_score'];
        });
        
        return $potentialMatches;
    }

    /**
     * Check the authenticity of a file against the expected achievement
     */
    private function checkFileAuthenticity($file, $achievement, $driveService)
    {
        $authenticity = [
            'file_name' => $file['name'],
            'achievement_type' => $achievement['label'],
            'content_match' => false,
            'confidence_level' => 'low',
            'file_type_valid' => false,
            'size_check' => false,
            'verification_notes' => []
        ];
        
        // Check if file is appropriate type for achievement
        $validExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $validExtensions)) {
            $authenticity['file_type_valid'] = true;
            $authenticity['verification_notes'][] = "Tipe file valid: {$fileExtension}";
        } else {
            // For Google native formats (docs, sheets, etc.), consider them valid too
            $nativeFormats = ['application/vnd.google-apps.document', 'application/vnd.google-apps.spreadsheet', 'application/vnd.google-apps.presentation'];
            if (in_array($file['mimeType'], $nativeFormats)) {
                $authenticity['file_type_valid'] = true;
                $authenticity['verification_notes'][] = "Format Google native: " . $file['mimeType'];
            } else {
                $authenticity['verification_notes'][] = "Tipe file tidak umum untuk sertifikat: {$fileExtension}";
            }
        }
        
        // Check if file has reasonable size (not too small or too large)
        // Some Google native files don't have size property
        if (isset($file['size']) && $file['size'] !== null) {
            if ($file['size'] > 1024 && $file['size'] < 50 * 1024 * 1024) { // Between 1KB and 50MB
                $authenticity['size_check'] = true;
            } else {
                $authenticity['verification_notes'][] = "Ukuran file mencurigakan: " . $this->formatBytes($file['size']);
            }
        } else {
            // For Google native formats, size info might not be available
            $authenticity['size_check'] = true; // Consider it valid if size is not available
            $authenticity['verification_notes'][] = "Ukuran tidak tersedia (mungkin file Google native)";
        }
        
        // For now, set content match as true if similarity score is high
        if ($file['similarity_score'] > 70) {
            $authenticity['content_match'] = true;
        }
        
        // Determine confidence level
        $validChecks = 0;
        if ($authenticity['file_type_valid']) $validChecks++;
        if ($authenticity['size_check']) $validChecks++;
        if ($authenticity['content_match']) $validChecks++;
        
        if ($validChecks >= 3) {
            $authenticity['confidence_level'] = 'high';
        } elseif ($validChecks >= 2) {
            $authenticity['confidence_level'] = 'medium';
        } else {
            $authenticity['confidence_level'] = 'low';
        }
        
        return $authenticity;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Generate expected file name based on achievement content
     */
    private function generateExpectedFileName($achievementLabel, $content)
    {
        // Extract keywords from content to form expected file names
        $keywords = explode(' ', str_replace(['.', ',', '!', '?'], ' ', strtolower($content)));
        $relevantKeywords = array_slice(array_filter($keywords), 0, 3);
        $expectedName = $achievementLabel . ' ' . implode(' ', $relevantKeywords);
        
        return substr($expectedName, 0, 50) . '.pdf'; // Example extension
    }
}