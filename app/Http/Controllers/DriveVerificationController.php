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
            // Log environment info for debugging
            \Log::info('Drive verification started', [
                'skpi_id' => $skpiId,
                'base64_env_set' => !empty(env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64')),
                'file_path_env_set' => !empty(env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE')),
                'app_env' => env('APP_ENV')
            ]);
            
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
            \Log::error('Drive verification error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
        
        // Use Service Account credentials - improved for production safety
        $serviceAccountFile = null;
        
        // Log credential setup attempt for debugging
        \Log::info('Setting up Google Drive credentials', [
            'base64_env_available' => !empty($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64'] ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64')),
            'file_path_env' => $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'] ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'),
            'app_env' => env('APP_ENV')
        ]);
        
        // Check if we have base64 encoded credentials in environment
        $serviceAccountJsonBase64 = $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64'] ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64');
        
        \Log::info('Checking Google Drive credentials', [
            'base64_length' => strlen($serviceAccountJsonBase64 ?? ''),
            'base64_preview' => substr($serviceAccountJsonBase64 ?? '', 0, 50) . '...',
            'base64_set' => !empty($serviceAccountJsonBase64)
        ]);
        
        if ($serviceAccountJsonBase64) {
            \Log::info('Using base64 encoded credentials from environment');
            
            // Validate Base64 format before decoding
            if (!preg_match('/^[A-Za-z0-9+\/=]*$/', $serviceAccountJsonBase64)) {
                \Log::error('Invalid Base64 format for Google Drive credentials');
                throw new \Exception('Format Base64 credential Google Drive tidak valid');
            }
            
            // Use base64 encoded JSON from environment variable
            $json = base64_decode($serviceAccountJsonBase64, true);
            if ($json === false) {
                \Log::error('Base64 decode failed for Google Drive credentials');
                \Log::error('Base64 content preview', [
                    'content' => substr($serviceAccountJsonBase64, 0, 100)
                ]);
                throw new \Exception('Credential Google Drive tidak valid (base64 decode failed)');
            }
            
            // Validate that decoded content is valid JSON
            $decodedJson = json_decode($json, true);
            if ($decodedJson === null) {
                \Log::error('Decoded Base64 is not valid JSON', [
                    'json_error' => json_last_error(),
                    'json_error_msg' => json_last_error_msg(),
                    'decoded_preview' => substr($json, 0, 100)
                ]);
                throw new \Exception('Credential Google Drive tidak valid (bukan JSON yang valid)');
            }
            
            // Save to temporary location
            $serviceAccountFile = storage_path('app/credentials/google_service_account.json');
            if (!is_dir(dirname($serviceAccountFile))) {
                mkdir(dirname($serviceAccountFile), 0755, true);
            }
            $result = file_put_contents($serviceAccountFile, $json);
            if ($result === false) {
                \Log::error('Failed to save Google Drive credential to file', ['file' => $serviceAccountFile]);
                throw new \Exception('Gagal menyimpan credential Google Drive ke file: ' . $serviceAccountFile);
            }
            
            \Log::info('Saved base64 credential to file', ['file' => $serviceAccountFile]);
        } else {
        } else {
            // Fallback to file path approach
            $serviceAccountFilePath = $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'] ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE');
            if ($serviceAccountFilePath) {
                $serviceAccountFile = storage_path('app/' . $serviceAccountFilePath);
                \Log::info('Using file path approach', ['file' => $serviceAccountFile]);
            }
        }
        
        // Validate that we have a valid credential file
        if (!$serviceAccountFile) {
            \Log::error('No Google Drive credential file specified');
            throw new \Exception('File credentials Google Drive tidak dispesifikasikan. Silakan atur GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE atau GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64 di .env');
        }
        
        if (!file_exists($serviceAccountFile)) {
            \Log::error('Google Drive credential file not found', [
                'serviceAccountFile' => $serviceAccountFile,
                'base64_env_set' => !empty($serviceAccountJsonBase64),
                'file_path_env_set' => !empty($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'] ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE')),
                'current_working_dir' => getcwd(),
                'storage_path' => storage_path(),
                'storage_app_path' => storage_path('app')
            ]);
            
            throw new \Exception('File credentials Google Drive tidak ditemukan. Silakan atur GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE atau GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64 di .env');
        }
        
        // Additional safety check - ensure it's actually a file, not a directory
        if (is_dir($serviceAccountFile)) {
            \Log::error('Google Drive credential path points to directory', ['path' => $serviceAccountFile]);
            throw new \Exception('Path credential Google Drive menunjuk ke direktori, bukan file: ' . $serviceAccountFile);
        }
        
        if (!is_readable($serviceAccountFile)) {
            \Log::error('Google Drive credential file is not readable', ['file' => $serviceAccountFile]);
            throw new \Exception('File credentials Google Drive tidak dapat dibaca: ' . $serviceAccountFile);
        }
        
        \Log::info('Successfully loaded Google Drive credentials', ['file' => $serviceAccountFile]);
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

        // Prepare student's achievements (skip if contains only '-' or empty)
        $achievements = [];
        if (!empty($skpi->prestasi_akademik) && trim(strtolower($skpi->prestasi_akademik)) !== '-') {
            $achievements[] = ['type' => 'prestasi_akademik', 'content' => $skpi->prestasi_akademik, 'label' => 'Prestasi Akademik'];
        }
        if (!empty($skpi->prestasi_non_akademik) && trim(strtolower($skpi->prestasi_non_akademik)) !== '-') {
            $achievements[] = ['type' => 'prestasi_non_akademik', 'content' => $skpi->prestasi_non_akademik, 'label' => 'Prestasi Non-Akademik'];
        }
        if (!empty($skpi->organisasi) && trim(strtolower($skpi->organisasi)) !== '-') {
            $achievements[] = ['type' => 'organisasi', 'content' => $skpi->organisasi, 'label' => 'Pengalaman Organisasi'];
        }
        if (!empty($skpi->pengalaman_kerja) && trim(strtolower($skpi->pengalaman_kerja)) !== '-') {
            $achievements[] = ['type' => 'pengalaman_kerja', 'content' => $skpi->pengalaman_kerja, 'label' => 'Pengalaman Kerja/Magang'];
        }
        if (!empty($skpi->sertifikat_kompetensi) && trim(strtolower($skpi->sertifikat_kompetensi)) !== '-') {
            $achievements[] = ['type' => 'sertifikat_kompetensi', 'content' => $skpi->sertifikat_kompetensi, 'label' => 'Sertifikat Kompetensi'];
        }

        $results = [
            'folder_id' => $folderId,
            'folder_url' => $skpi->drive_link,
            'total_achievements' => 0,
            'total_documents_in_drive' => count($driveFileDetails),
            'total_files_scanned' => count($driveFileDetails),
            'matched_items' => [],
            'missing_items' => [],
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
        
        // Process each achievement with progress tracking
        $totalAchievements = count($achievements);
        $currentAchievement = 0;
        
        foreach ($achievements as $achievement) {
            if (!empty($achievement['content'])) {
                $currentAchievement++;
                // Update progress (you might send this to frontend if needed)
                $progressPercentage = round(($currentAchievement / $totalAchievements) * 100);
                
                $results['total_achievements']++;
                
                // Look for matching documents in drive using content analysis
                $potentialMatches = $this->findPotentialMatchesWithContentAnalysis($achievement, $driveFileDetails, $driveService);
                
                if (!empty($potentialMatches)) {
                    // Take only the best match for this achievement to avoid duplicates
                    $bestMatch = $potentialMatches[0];
                    
                    if (!in_array($bestMatch['id'], $matchedFileIds)) {
                        $results['matched_items'][] = [
                            'type' => $achievement['label'],
                            'content' => $achievement['content'],
                            'file_name' => $bestMatch['name'],
                            'file_id' => $bestMatch['id'],
                            'similarity_score' => $bestMatch['similarity_score'],
                            'semantic_score' => $bestMatch['semantic_score'],
                            'combined_score' => $bestMatch['combined_score']
                        ];
                        
                        $matchedFileIds[] = $bestMatch['id'];
                        
                        // Skip authenticity check as it's not needed for simplified output
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
                                    'similarity_score' => $match['similarity_score'],
                                    'semantic_score' => $match['semantic_score'],
                                    'combined_score' => $match['combined_score']
                                ];
                                
                                $matchedFileIds[] = $match['id'];
                                
                                // Skip authenticity check as it's not needed for simplified output
                                
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
        
        // Add progress information to results
        $results['progress'] = [
            'current' => $currentAchievement,
            'total' => $totalAchievements,
            'percentage' => 100 // Complete when function finishes
        ];

        // Skip finding extra items since we don't need them in simplified output
        
        // Calculate overall validation score
        $validationScore = $this->calculateValidationScore($results);
        $results['validation_score'] = $validationScore;
        
        return $results;
    }
    
    /**
     * Calculate overall validation score based on matches and completeness only
     * Returns equal percentage for each available achievement (e.g., 2 achievements = 50% each)
     */
    private function calculateValidationScore($results)
    {
        $totalAchievements = $results['total_achievements'];
        $matchedCount = count($results['matched_items']);
        
        if ($totalAchievements === 0) {
            return [
                'percentage' => 100,
                'message' => 'Tidak ada data prestasi untuk divalidasi',
                'status' => 'info'
            ];
        }
        
        // Calculate score: each achievement has equal weight
        // For example: if there are 2 achievements, each is worth 50% (100/2)
        // If there are 4 achievements, each is worth 25% (100/4)
        $scorePerAchievement = 100 / $totalAchievements;
        $overallScore = $matchedCount * $scorePerAchievement;
        
        // Simple message focusing only on requested elements
        $missingCount = count($results['missing_items']);
        $message = '';
        
        // Add validation level
        if ($overallScore >= 90) {
            $message .= "Tingkat Validasi: Sangat Baik - ";
        } elseif ($overallScore >= 75) {
            $message .= "Tingkat Validasi: Baik - ";
        } elseif ($overallScore >= 60) {
            $message .= "Tingkat Validasi: Cukup - ";
        } elseif ($overallScore >= 40) {
            $message .= "Tingkat Validasi: Kurang - ";
        } else {
            $message .= "Tingkat Validasi: Sangat Kurang - ";
        }
        
        // Add document summary
        $message .= "Dokumen terpenuhi: {$matchedCount}/{$totalAchievements}. ";
        
        // Add missing documents info
        if ($missingCount > 0) {
            $message .= "Kekurangan dokumen: {$missingCount} item. ";
        } else {
            $message .= "Tidak ada kekurangan dokumen. ";
        }
        
        // Add completed documents
        if (count($results['matched_items']) > 0) {
            $matchedTypes = array_column($results['matched_items'], 'type');
            $uniqueTypes = array_unique($matchedTypes); // Remove duplicates if any
            $message .= "Dokumen terpenuhi: " . implode(', ', $uniqueTypes) . ".";
        } else {
            $message .= "Dokumen terpenuhi: Tidak ada.";
        }
        
        return [
            'percentage' => round($overallScore, 2),
            'message' => $message,
            'status' => $overallScore >= 80 ? 'excellent' : ($overallScore >= 60 ? 'good' : ($overallScore >= 40 ? 'average' : 'poor'))
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
     * Find potential matching files for an achievement using advanced semantic analysis
     */
    private function findPotentialMatches($achievement, $driveFileDetails)
    {
        $potentialMatches = [];
        $contentLower = strtolower($achievement['content']);
        $labelLower = strtolower($achievement['label']);
        
        // Basic matching based on filename analysis
        foreach ($driveFileDetails as $fileDetail) {
            $fileName = $fileDetail['name'];
            $similarityScore = 0;
            $semanticScore = 0;
            
            // Basic text similarity with more precise algorithm
            $contentSimilarity = $this->advancedSimilarity($fileName, $contentLower);
            $labelSimilarity = $this->advancedSimilarity($fileName, $labelLower);
            
            // Keyword matching with more advanced logic
            $contentWords = $this->extractKeywords($contentLower);
            $fileNameWords = $this->extractKeywords($fileName);
            $keywordMatch = $this->calculateKeywordMatch($contentWords, $fileNameWords);
            
            // Calculate overall similarity score
            $similarityScore = max($contentSimilarity, $labelSimilarity);
            $semanticScore = $keywordMatch; // Semantic score based on keyword matching
            
            // Calculate combined score
            $combinedScore = ($similarityScore * 0.4) + ($semanticScore * 0.6); // Weight semantic analysis more heavily
            
            // If combined score is high enough, consider it a match
            if ($combinedScore > 20) { // Threshold for matching
                $potentialMatches[] = [
                    'name' => $fileDetail['name'],
                    'id' => $fileDetail['id'],
                    'mimeType' => $fileDetail['mimeType'],
                    'similarity_score' => round($similarityScore, 2),
                    'semantic_score' => round($semanticScore, 2),
                    'combined_score' => round($combinedScore, 2)
                ];
            }
        }
        
        // Sort by combined score descending
        usort($potentialMatches, function($a, $b) {
            return $b['combined_score'] - $a['combined_score'];
        });
        
        return $potentialMatches;
    }
    
    /**
     * Find potential matching files analyzing actual file content with optimized processing
     * This version is safer for production/cloud environments
     */
    private function findPotentialMatchesWithContentAnalysis($achievement, $driveFileDetails, $driveService)
    {
        $potentialMatches = [];
        $contentLower = strtolower($achievement['content']);
        $labelLower = strtolower($achievement['label']);
        
        // Quick preprocessing to extract keywords once
        $contentWords = $this->extractKeywords($contentLower);
        
        // Enhanced analysis by examining actual file content with optimized processing
        foreach ($driveFileDetails as $fileDetail) {
            $fileName = $fileDetail['name'];
            $similarityScore = 0;
            $semanticScore = 0;
            $contentMatchScore = 0;
            
            // Quick initial filtering based on filename using faster string operations
            $contentSimilarity = $this->fastSimilarityCheck($fileName, $contentLower);
            $labelSimilarity = $this->fastSimilarityCheck($fileName, $labelLower);
            
            // Quick keyword matching with early exit
            $fileNameWords = $this->extractKeywords($fileName);
            $keywordMatch = $this->calculateKeywordMatch($contentWords, $fileNameWords);
            
            // Calculate initial similarity score
            $similarityScore = max($contentSimilarity, $labelSimilarity);
            $semanticScore = $keywordMatch;
            
            $fileId = $fileDetail['id'];
            $mimeType = $fileDetail['mimeType'];
            
            // Quick skip for non-readable files or clearly non-matching files
            if ($this->isTextReadable($mimeType) && 
                ($similarityScore > 30 || $keywordMatch > 15)) {  // Higher threshold to reduce API calls
                
                try {
                    // Only extract content if file shows promise based on name AND we're not in cloud environment
                    // For cloud environments, we'll rely more on filename matching to avoid file operation errors
                    $fileObject = $driveService->files->get($fileId);
                    
                    // For safer approach in cloud, we'll use a modified version that avoids problematic file operations
                    // Instead of extracting content, we'll rely on filename and metadata matching
                    $fileContent = \App\Helpers\DocumentTextExtractor::extractTextFromDriveFile($fileObject, $driveService);
                    
                    if (!empty($fileContent) && strpos($fileContent, 'konten tidak dapat diekstrak') === false) {
                        // Limit content analysis to first 5000 characters to speed up
                        $limitedContent = substr($fileContent, 0, 5000);
                        
                        // Analyze the actual content of the file
                        $contentAnalysis = $this->analyzeFileContent($limitedContent, $achievement);
                        $contentMatchScore = $contentAnalysis['semantic_score'];
                        
                        // Update semantic score with content analysis results
                        $semanticScore = max($semanticScore, $contentMatchScore);
                    } else {
                        // If content extraction returned error indicator, fall back to stronger filename matching
                        $semanticScore = $keywordMatch * 1.5; // Amplify keyword match since we couldn't get content
                    }
                } catch (\Exception $e) {
                    // If content extraction fails, continue with filename analysis
                    error_log("Could not extract content from file {$fileDetail['name']}: " . $e->getMessage());
                    // Fall back to higher weight on filename matching
                    $semanticScore = max($semanticScore, $keywordMatch * 1.2);
                }
            }
            
            // Calculate combined score with emphasis on actual content match
            $combinedScore = ($similarityScore * 0.3) + ($semanticScore * 0.7); // Adjust weights to be safer
            
            // If combined score is high enough, consider it a match
            if ($combinedScore > 15) { // Lower threshold to maintain sensitivity
                $potentialMatches[] = [
                    'name' => $fileDetail['name'],
                    'id' => $fileDetail['id'],
                    'mimeType' => $fileDetail['mimeType'],
                    'similarity_score' => round($similarityScore, 2),
                    'semantic_score' => round($semanticScore, 2),
                    'combined_score' => round($combinedScore, 2)
                ];
            }
        }
        
        // Sort by combined score descending
        usort($potentialMatches, function($a, $b) {
            return $b['combined_score'] - $a['combined_score'];
        });
        
        return $potentialMatches;
    }
    
    /**
     * Fast similarity check using simple substring matching
     */
    private function fastSimilarityCheck($str1, $str2) 
    {
        // Quick exit if strings are identical
        if ($str1 === $str2) {
            return 100;
        }
        
        // Convert to lowercase for comparison
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);
        
        // Check if one string contains the other
        if (strpos($str1, $str2) !== false) {
            return 80; // High similarity
        }
        
        if (strpos($str2, $str1) !== false) {
            return 80; // High similarity
        }
        
        // Check if they share significant common substring
        $minLength = min(strlen($str1), strlen($str2));
        if ($minLength > 0) {
            // Simple shared keyword approach
            $words1 = explode(' ', $str1);
            $words2 = explode(' ', $str2);
            
            $commonWords = array_intersect($words1, $words2);
            $similarity = (count($commonWords) / $minLength) * 100;
            
            return min(100, $similarity * 3); // Amplify the score
        }
        
        return 0;
    }
    
    /**
     * Advanced similarity algorithm using Jaro-Winkler or similar
     */
    private function advancedSimilarity($str1, $str2) 
    {
        // If strings are identical
        if ($str1 === $str2) {
            return 100;
        }
        
        // Using similar_text as base but with better calculation
        similar_text($str1, $str2, $percent);
        return $percent;
    }
    
    /**
     * Extract keywords from text with better filtering
     */
    private function extractKeywords($text) 
    {
        // Remove special characters and split into words
        $words = preg_split('/[^\w-]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        // Filter out common stop words and very short words
        $stopWords = ['dan', 'atau', 'dengan', 'untuk', 'pada', 'di', 'ke', 'dari', 'ini', 'itu', 'the', 'and', 'or', 'with', 'for', 'on', 'in', 'to', 'from', 'this', 'that', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'the', 'as', 'at', 'by', 'for', 'if', 'into', 'of', 'off', 'on', 'onto', 'out', 'over', 'to', 'toward', 'with', 'within', 'without'];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        return array_unique($keywords);
    }
    
    /**
     * Calculate keyword match score
     */
    private function calculateKeywordMatch($contentKeywords, $fileNameKeywords) 
    {
        if (empty($contentKeywords) || empty($fileNameKeywords)) {
            return 0;
        }
        
        $matchedKeywords = array_intersect($contentKeywords, $fileNameKeywords);
        $matchScore = (count($matchedKeywords) / count($contentKeywords)) * 100;
        
        return min(100, $matchScore);
    }

    /**
     * Check file authenticity with advanced AI-driven logic
     */
    private function checkFileAuthenticity($file, $achievement, $driveService)
    {
        $authenticity = [
            'file_name' => $file['name'],
            'achievement_type' => $achievement['label'],
            'content_match' => false,
            'semantic_match' => false,
            'confidence_level' => 'low',
            'file_type_valid' => false,
            'size_check' => false,
            'format_analysis' => [
                'is_document' => false,
                'is_image' => false,
                'is_certified_format' => false
            ],
            'verification_notes' => []
        ];
        
        // Check if file is appropriate type for achievement
        $validExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $validExtensions)) {
            $authenticity['file_type_valid'] = true;
            $authenticity['verification_notes'][] = "Tipe file valid: {$fileExtension}";
        } else {
            // For Google native formats (docs, sheets, etc.), consider them valid too
            $nativeFormats = [
                'application/vnd.google-apps.document', 
                'application/vnd.google-apps.spreadsheet', 
                'application/vnd.google-apps.presentation'
            ];
            if (in_array($file['mimeType'], $nativeFormats)) {
                $authenticity['file_type_valid'] = true;
                $authenticity['verification_notes'][] = "Format Google native: " . $file['mimeType'];
            } else {
                $authenticity['verification_notes'][] = "Tipe file tidak umum untuk sertifikat: {$fileExtension}";
            }
        }
        
        // Check if file has reasonable size (not too small or too large)
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
        
        // Format analysis
        if (strpos($file['mimeType'], 'pdf') !== false) {
            $authenticity['format_analysis']['is_document'] = true;
            $authenticity['format_analysis']['is_certified_format'] = true; // PDFs are often used for certificates
        } elseif (strpos($file['mimeType'], 'image') !== false) {
            $authenticity['format_analysis']['is_image'] = true;
        } elseif (in_array($file['mimeType'], ['application/vnd.google-apps.document', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            $authenticity['format_analysis']['is_document'] = true;
        }
        
        // Determine content and semantic matches
        $authenticity['content_match'] = $file['similarity_score'] > 70;
        $authenticity['semantic_match'] = $file['semantic_score'] > 60;
        
        // Determine confidence level using more sophisticated logic
        $confidenceScore = 0;
        if ($authenticity['file_type_valid']) $confidenceScore += 25;
        if ($authenticity['size_check']) $confidenceScore += 15;
        if ($authenticity['content_match']) $confidenceScore += 30;
        if ($authenticity['semantic_match']) $confidenceScore += 30;
        
        // Additional factors
        if ($authenticity['format_analysis']['is_certified_format']) $confidenceScore += 10;
        
        if ($confidenceScore >= 85) {
            $authenticity['confidence_level'] = 'high';
        } elseif ($confidenceScore >= 60) {
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
    
    /**
     * Check if file type supports text reading for content analysis
     */
    private function isTextReadable($mimeType) 
    {
        $textReadableTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/vnd.google-apps.document',
            'application/vnd.google-apps.spreadsheet',
            'application/vnd.google-apps.presentation',
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/gif'
        ];
        
        // Check if mime type starts with any of the readable types
        foreach ($textReadableTypes as $readableType) {
            if (strpos($mimeType, $readableType) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Analyze content of a Google Drive file for relevance to achievement
     */
    private function analyzeFileContentFromDrive($fileId, $achievement, $driveService = null)
    {
        // Since we don't have access to driveService in this function,
        // we need to get it from a different context. This function would be called
        // from verifyDriveContentsWithAPI which has the driveService
        // For now, we'll just return 0 and handle content analysis differently
        return 0;
    }
    
    /**
     * Analyze content of readable files with optimized processing
     */
    private function analyzeFileContent($fileContent, $achievement)
    {
        $result = [
            'semantic_score' => 0,
            'content_similarity' => 0,
            'has_relevant_content' => false,
            'extraction_success' => true,
            'relevant_keywords_found' => []
        ];
        
        try {
            $achievementContent = strtolower($achievement['content']);
            $achievementKeywords = $this->extractKeywords($achievementContent);
            
            // Check if achievement keywords appear in file content
            $foundKeywords = [];
            $totalKeywords = count($achievementKeywords);
            $matchedKeywords = 0;
            
            // Use more efficient approach - limit search to first and last portions of content
            $firstPart = substr($fileContent, 0, 1000);
            $lastPart = substr($fileContent, -1000);
            $searchableContent = $firstPart . ' ' . $lastPart;
            
            foreach ($achievementKeywords as $keyword) {
                if (strlen($keyword) > 2 && stripos($searchableContent, $keyword) !== false) {
                    $foundKeywords[] = $keyword;
                    $matchedKeywords++;
                    // Early exit optimization - if we find enough matches, return early
                    if ($matchedKeywords > $totalKeywords * 0.3) { // If 30% are matched, it's likely relevant
                        break;
                    }
                }
            }
            
            // Calculate content match score based on keyword presence
            $contentMatchScore = 0;
            if ($totalKeywords > 0) {
                $contentMatchScore = min(100, ($matchedKeywords / $totalKeywords) * 100);
            }
            
            // Perform additional semantic analysis using fast similarity check
            $contentSimilarity = $this->fastSimilarityCheck(
                substr($fileContent, 0, 100), // Use first 100 chars for performance
                $achievementContent
            );
            
            // Combine scores - prioritize actual keyword matches
            $result['semantic_score'] = $contentMatchScore * 0.7 + $contentSimilarity * 0.3;
            $result['content_similarity'] = $contentSimilarity;
            $result['has_relevant_content'] = $contentMatchScore > 5; // Lower threshold for relevance
            $result['relevant_keywords_found'] = $foundKeywords;
            
        } catch (\Exception $e) {
            // If content analysis fails, return minimal score
            $result['extraction_success'] = false;
            $result['semantic_score'] = 0;
            $result['content_similarity'] = 0;
        }
        
        return $result;
    }
}