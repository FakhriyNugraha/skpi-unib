<?php

namespace App\Http\Controllers;

use App\Models\SkpiData;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;
use OpenAI\Laravel\Facades\OpenAI;

class DriveVerificationController extends Controller
{
    /**
     * Verify the contents of the Google Drive folder against the student's data
     */
    public function verifyDriveContents(Request $request, $skpiId)
    {
        $skpi = SkpiData::with(['user', 'jurusan'])->findOrFail($skpiId);

        // Validasi izin akses (opsional)
        // $request->user()->can('view', $skpi);

        if (!$skpi->drive_link) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada link Google Drive yang disediakan'
            ]);
        }

        try {
            \Log::info('Drive verification started', [
                'skpi_id' => $skpiId,
                'base64_env_set' => !empty(env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64')),
                'file_path_env_set' => !empty(env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE')),
                'app_env' => env('APP_ENV')
            ]);

            // Ambil folder ID dari URL
            $folderId = $this->extractFolderId($skpi->drive_link);
            if (!$folderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengekstrak ID folder dari URL Google Drive'
                ]);
            }

            // Verifikasi isi folder via Google Drive API
            $results = $this->verifyDriveContentsWithAPI($skpi, $folderId);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Drive verification error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi: '.$e->getMessage()
            ]);
        }
    }

    /**
     * Extract folder ID from Google Drive URL
     */
    private function extractFolderId($url)
    {
        $pattern = '/(?:folders\/|id=|\/d\/)([a-zA-Z0-9_-]+)/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Verify drive contents using Google Drive API with recursive folder scanning and content-based matching
     */
    private function verifyDriveContentsWithAPI($skpi, $folderId)
    {
        // Init Google Client
        $client = new Client();

        // Setup kredensial akun layanan
        $serviceAccountFile = null;

        \Log::info('Setting up Google Drive credentials', [
            'base64_env_available' => !empty(isset($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64']) ? $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64'] : env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64')),
            'file_path_env' => isset($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE']) ? $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'] : env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'),
            'app_env' => env('APP_ENV')
        ]);

        $serviceAccountJsonBase64 = isset($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64'])
            ? $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64']
            : env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64');

        \Log::info('Checking Google Drive credentials', [
            'base64_length' => strlen($serviceAccountJsonBase64 !== null ? $serviceAccountJsonBase64 : ''),
            'base64_preview' => substr($serviceAccountJsonBase64 !== null ? $serviceAccountJsonBase64 : '', 0, 50) . '...',
            'base64_set' => !empty($serviceAccountJsonBase64)
        ]);

        if ($serviceAccountJsonBase64) {
            \Log::info('Using base64 encoded credentials from environment');

            if (!preg_match('/^[A-Za-z0-9+\/=]*$/', $serviceAccountJsonBase64)) {
                \Log::error('Invalid Base64 format for Google Drive credentials');
                throw new \Exception('Format Base64 credential Google Drive tidak valid');
            }

            $json = base64_decode($serviceAccountJsonBase64, true);
            if ($json === false) {
                \Log::error('Base64 decode failed for Google Drive credentials', [
                    'content_preview' => substr($serviceAccountJsonBase64, 0, 100)
                ]);
                throw new \Exception('Credential Google Drive tidak valid (base64 decode failed)');
            }

            $decodedJson = json_decode($json, true);
            if ($decodedJson === null) {
                \Log::error('Decoded Base64 is not valid JSON', [
                    'json_error' => json_last_error(),
                    'json_error_msg' => json_last_error_msg(),
                    'decoded_preview' => substr($json, 0, 100)
                ]);
                throw new \Exception('Credential Google Drive tidak valid (bukan JSON yang valid)');
            }

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
            $serviceAccountFilePath = isset($_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE'])
                ? $_ENV['GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE']
                : env('GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE');

            if ($serviceAccountFilePath) {
                $serviceAccountFile = storage_path('app/' . $serviceAccountFilePath);
                \Log::info('Using file path approach', ['file' => $serviceAccountFile]);
            }
        }

        if (!$serviceAccountFile) {
            \Log::warning('Google Drive credential not configured, returning basic file info only');
            // Jika credentials tidak disiapkan, hanya kembalikan informasi basic tanpa verifikasi
            return [
                'folder_id' => $folderId,
                'folder_url' => $skpi->drive_link,
                'total_achievements' => 0,
                'total_documents_in_drive' => 0,
                'total_files_scanned' => 0,
                'matched_items' => [],
                'missing_items' => [],
                'status' => 'skipped',
                'message' => 'Verifikasi Google Drive tidak aktif karena konfigurasi belum lengkap. Fitur ini akan aktif jika administrator mengkonfigurasi GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE atau GOOGLE_DRIVE_SERVICE_JSON_BASE64 di .env.',
                'scan_summary' => [
                    'pdf_count' => 0,
                    'image_count' => 0,
                    'document_count' => 0
                ],
                'progress' => [
                    'current' => 0,
                    'total' => 0,
                    'percentage' => 0
                ],
                'validation_score' => [
                    'percentage' => 0,
                    'message' => 'Verifikasi Google Drive sedang dinonaktifkan. Untuk mengaktifkan: 1) Buat service account di Google Cloud Console 2) Aktifkan Google Drive API 3) Unduh credential JSON 4) Encode ke base64 dan taruh di variabel GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64 di .env',
                    'details' => [],
                    'status' => 'info'
                ]
            ];
        }

        if (!file_exists($serviceAccountFile)) {
            \Log::warning('Google Drive credential file not found, returning basic info', [
                'serviceAccountFile' => $serviceAccountFile
            ]);
            // Jika file credentials tidak ditemukan, hanya kembalikan informasi basic
            return [
                'folder_id' => $folderId,
                'folder_url' => $skpi->drive_link,
                'total_achievements' => 0,
                'total_documents_in_drive' => 0,
                'total_files_scanned' => 0,
                'matched_items' => [],
                'missing_items' => [],
                'status' => 'skipped',
                'message' => 'File credential Google Drive tidak ditemukan. Atur GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE atau GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64 di .env',
                'scan_summary' => [
                    'pdf_count' => 0,
                    'image_count' => 0,
                    'document_count' => 0
                ],
                'progress' => [
                    'current' => 0,
                    'total' => 0,
                    'percentage' => 0
                ],
                'validation_score' => [
                    'percentage' => 0,
                    'message' => 'Verifikasi Google Drive sedang dinonaktifkan karena file credential tidak ditemukan. Pastikan variabel GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64 di .env berisi credential base64 yang valid',
                    'details' => [],
                    'status' => 'info'
                ]
            ];
        }

        if (is_dir($serviceAccountFile)) {
            \Log::warning('Google Drive credential path points to directory, returning basic info', ['path' => $serviceAccountFile]);
            return [
                'folder_id' => $folderId,
                'folder_url' => $skpi->drive_link,
                'total_achievements' => 0,
                'total_documents_in_drive' => 0,
                'total_files_scanned' => 0,
                'matched_items' => [],
                'missing_items' => [],
                'status' => 'skipped',
                'message' => "Path credential Google Drive menunjuk ke direktori, bukan file: {$serviceAccountFile}",
                'scan_summary' => [
                    'pdf_count' => 0,
                    'image_count' => 0,
                    'document_count' => 0
                ],
                'progress' => [
                    'current' => 0,
                    'total' => 0,
                    'percentage' => 0
                ],
                'validation_score' => [
                    'percentage' => 0,
                    'message' => 'Verifikasi Google Drive gagal karena path credential tidak valid. Path harus mengarah ke file credential, bukan direktori.',
                    'details' => [],
                    'status' => 'info'
                ]
            ];
        }

        if (!is_readable($serviceAccountFile)) {
            \Log::warning('Google Drive credential file is not readable, returning basic info', ['file' => $serviceAccountFile]);
            return [
                'folder_id' => $folderId,
                'folder_url' => $skpi->drive_link,
                'total_achievements' => 0,
                'total_documents_in_drive' => 0,
                'total_files_scanned' => 0,
                'matched_items' => [],
                'missing_items' => [],
                'status' => 'skipped',
                'message' => "File credentials Google Drive tidak dapat dibaca: {$serviceAccountFile}",
                'scan_summary' => [
                    'pdf_count' => 0,
                    'image_count' => 0,
                    'document_count' => 0
                ],
                'progress' => [
                    'current' => 0,
                    'total' => 0,
                    'percentage' => 0
                ],
                'validation_score' => [
                    'percentage' => 0,
                    'message' => 'Verifikasi Google Drive sedang dinonaktifkan karena file credential tidak dapat diakses. Pastikan file credential memiliki izin akses yang tepat.',
                    'details' => [],
                    'status' => 'info'
                ]
            ];
        }

        \Log::info('Successfully loaded Google Drive credentials', ['file' => $serviceAccountFile]);
        $client->setAuthConfig($serviceAccountFile);
        $client->addScope(Drive::DRIVE_READONLY);

        $driveService = new Drive($client);

        // Ambil semua file (rekursif)
        $allFiles = $this->getAllFilesRecursively($driveService, $folderId);

        $driveFileDetails = [];
        foreach ($allFiles as $file) {
            $driveFileDetails[] = [
                'name' => strtolower($file->getName()),
                'id' => $file->getId(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize() !== null ? $file->getSize() : null,
                'modifiedTime' => $file->getModifiedTime(),
                'parents' => $file->getParents()
            ];
        }

        // Kumpulkan data prestasi (skip jika hanya "-" atau kosong)
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

        foreach ($driveFileDetails as $file) {
            if (strpos($file['mimeType'], 'pdf') !== false) {
                $results['scan_summary']['pdf_count']++;
            } elseif (strpos($file['mimeType'], 'image') !== false) {
                $results['scan_summary']['image_count']++;
            } elseif (
                strpos($file['mimeType'], 'document') !== false ||
                strpos($file['mimeType'], 'sheet') !== false ||
                strpos($file['mimeType'], 'presentation') !== false
            ) {
                $results['scan_summary']['document_count']++;
            }
        }

        $matchedFileIds = [];
        $totalAchievements = count($achievements);

        if ($totalAchievements > 0) {
            foreach ($achievements as $idx => $achievement) {
                if (!empty($achievement['content'])) {
                    $progressPercentage = round((($idx + 1) / $totalAchievements) * 100);

                    \Log::info("Processing achievement ".($idx + 1)."/{$totalAchievements}", [
                        'type' => $achievement['label'],
                        'progress' => $progressPercentage.'%'
                    ]);

                    $results['total_achievements']++;

                    $potentialMatches = $this->findPotentialMatchesWithContentAnalysis(
                        $achievement,
                        $driveFileDetails,
                        $driveService
                    );

                    if (!empty($potentialMatches)) {
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
                        } else {
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
        }

        $results['progress'] = [
            'current' => $totalAchievements,
            'total' => $totalAchievements,
            'percentage' => 100
        ];

        $validationScore = $this->calculateValidationScore($results);
        $results['validation_score'] = $validationScore;

        return $results;
    }

    /**
     * Hitung skor validasi keseluruhan
     */
    private function calculateValidationScore($results)
    {
        $totalAchievements = $results['total_achievements'];
        $matchedCount = count($results['matched_items']);

        if ($totalAchievements === 0) {
            return [
                'percentage' => 100,
                'message' => 'Tidak ada data prestasi untuk divalidasi',
                'details' => [],
                'status' => 'info'
            ];
        }

        $scorePerAchievement = 100 / $totalAchievements;
        $overallScore = $matchedCount * $scorePerAchievement;

        $details = [];
        $achievementTypes = [
            'prestasi_akademik' => 'Prestasi Akademik',
            'prestasi_non_akademik' => 'Prestasi Non-Akademik',
            'organisasi' => 'Pengalaman Organisasi',
            'pengalaman_kerja' => 'Pengalaman Kerja/Magang',
            'sertifikat_kompetensi' => 'Sertifikat Kompetensi'
        ];

        foreach ($achievementTypes as $type => $label) {
            $matchedItem = null;
            foreach ($results['matched_items'] as $item) {
                if (strpos(strtolower($item['type']), strtolower($label)) !== false) {
                    $matchedItem = $item;
                    break;
                }
            }

            $isMatched = $matchedItem !== null;
            $details[] = [
                'type' => $label,
                'status' => $isMatched ? 'tersedia' : 'tidak tersedia',
                'percentage' => $isMatched ? round($scorePerAchievement, 2) : 0,
                'file_name' => $isMatched ? $matchedItem['file_name'] : null,
                'confidence' => $isMatched ? round($matchedItem['combined_score'], 2) : 0
            ];
        }

        $missingCount = count($results['missing_items']);
        $message = '';

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

        $completionPercentage = ($matchedCount / $totalAchievements) * 100;
        $message .= "Dokumen terpenuhi: {$matchedCount}/{$totalAchievements} (" . round($completionPercentage, 1) . "%). ";

        if ($missingCount > 0) {
            $message .= "Kekurangan dokumen: {$missingCount} item. ";
        } else {
            $message .= "Tidak ada kekurangan dokumen. ";
        }

        if (count($results['matched_items']) > 0) {
            $matchedTypes = array_column($results['matched_items'], 'type');
            $uniqueTypes = array_unique($matchedTypes);
            $message .= "Dokumen terpenuhi: " . implode(', ', $uniqueTypes) . ".";
        } else {
            $message .= "Dokumen terpenuhi: Tidak ada.";
        }

        return [
            'percentage' => round($overallScore, 2),
            'message' => $message,
            'details' => $details,
            'status' => $overallScore >= 80 ? 'excellent' : ($overallScore >= 60 ? 'good' : ($overallScore >= 40 ? 'average' : 'poor'))
        ];
    }

    /**
     * Ambil semua file dalam folder dan subfolder (rekursif)
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

        // Subfolder
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
     * (opsional) Pencocokan berbasis nama file saja
     */
    private function findPotentialMatches($achievement, $driveFileDetails)
    {
        $potentialMatches = [];
        $contentLower = strtolower($achievement['content']);
        $labelLower = strtolower($achievement['label']);

        foreach ($driveFileDetails as $fileDetail) {
            $fileName = $fileDetail['name'];

            $contentSimilarity = $this->advancedSimilarity($fileName, $contentLower);
            $labelSimilarity = $this->advancedSimilarity($fileName, $labelLower);

            $contentWords = $this->extractKeywords($contentLower);
            $fileNameWords = $this->extractKeywords($fileName);
            $keywordMatch = $this->calculateKeywordMatch($contentWords, $fileNameWords);

            $similarityScore = max($contentSimilarity, $labelSimilarity);
            $semanticScore = $keywordMatch;
            $combinedScore = ($similarityScore * 0.4) + ($semanticScore * 0.6);

            if ($combinedScore > 20) {
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

        usort($potentialMatches, function ($a, $b) {
            return $b['combined_score'] <=> $a['combined_score'];
        });

        return $potentialMatches;
    }

    /**
     * Pencocokan dengan analisis konten file (optimized)
     */
    private function findPotentialMatchesWithContentAnalysis($achievement, $driveFileDetails, $driveService)
    {
        $potentialMatches = [];
        $contentLower = strtolower($achievement['content']);
        $labelLower = strtolower($achievement['label']);

        $contentWords = $this->extractKeywords($contentLower);

        $filteredFiles = [];
        foreach ($driveFileDetails as $fileDetail) {
            $fileName = $fileDetail['name'];

            $contentSimilarity = $this->fastSimilarityCheck($fileName, $contentLower);
            $labelSimilarity = $this->fastSimilarityCheck($fileName, $labelLower);

            $fileNameWords = $this->extractKeywords($fileName);
            $keywordMatch = $this->calculateKeywordMatch($contentWords, $fileNameWords);

            $similarityScore = max($contentSimilarity, $labelSimilarity);
            $semanticScore = $keywordMatch;

            if ($similarityScore > 20 || $keywordMatch > 10) {
                $combinedScore = ($similarityScore * 0.4) + ($semanticScore * 0.6);
                $filteredFiles[] = [
                    'file_detail' => $fileDetail,
                    'combined_score' => $combinedScore,
                    'similarity_score' => $similarityScore,
                    'semantic_score' => $semanticScore
                ];
            }
        }

        usort($filteredFiles, function ($a, $b) {
            return $b['combined_score'] <=> $a['combined_score'];
        });

        $topFiles = array_slice($filteredFiles, 0, 20);

        foreach ($topFiles as $filteredFile) {
            $fileDetail = $filteredFile['file_detail'];
            $similarityScore = $filteredFile['similarity_score'];
            $semanticScore = $filteredFile['semantic_score'];
            $contentMatchScore = 0;

            $mimeType = $fileDetail['mimeType'];
            $fileId = $fileDetail['id'];

            if ($this->isTextReadable($mimeType) && ($similarityScore > 30 || $semanticScore > 15)) {
                try {
                    $fileObject = $driveService->files->get($fileId);

                    $fileContent = \App\Helpers\DocumentTextExtractor::extractTextFromDriveFile($fileObject, $driveService);

                    if (!empty($fileContent) && strpos($fileContent, 'konten tidak dapat diekstrak') === false) {
                        $limitedContent = substr($fileContent, 0, 3000);

                        $contentAnalysis = $this->analyzeFileContent($limitedContent, $achievement);
                        $contentMatchScore = $contentAnalysis['semantic_score'];

                        $semanticScore = max($semanticScore, $contentMatchScore);
                    } else {
                        $semanticScore = $semanticScore * 1.5;
                    }
                } catch (\Exception $e) {
                    \Log::warning("Content extraction failed for {$fileDetail['name']}: ".$e->getMessage());
                    $semanticScore = max($semanticScore, $semanticScore * 1.2);
                }
            }

            $combinedScore = ($similarityScore * 0.3) + ($semanticScore * 0.7);

            if ($combinedScore > 20) {
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

        usort($potentialMatches, function ($a, $b) {
            return $b['combined_score'] <=> $a['combined_score'];
        });

        return $potentialMatches;
    }

    /**
     * Fast similarity check menggunakan substring/keyword sederhana
     */
    private function fastSimilarityCheck($str1, $str2)
    {
        if ($str1 === $str2) {
            return 100;
        }

        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        if (strpos($str1, $str2) !== false) {
            return 80;
        }
        if (strpos($str2, $str1) !== false) {
            return 80;
        }

        $minLength = min(strlen($str1), strlen($str2));
        if ($minLength > 0) {
            $words1 = preg_split('/\s+/', $str1, -1, PREG_SPLIT_NO_EMPTY);
            $words2 = preg_split('/\s+/', $str2, -1, PREG_SPLIT_NO_EMPTY);

            $commonWords = array_intersect($words1, $words2);
            $similarity = (count($commonWords) / max(1, $minLength)) * 100;

            return min(100, $similarity * 3);
        }

        return 0;
        }

    /**
     * Advanced similarity (menggunakan similar_text)
     */
    private function advancedSimilarity($str1, $str2)
    {
        if ($str1 === $str2) {
            return 100;
        }
        similar_text($str1, $str2, $percent);
        return $percent;
    }

    /**
     * Ekstraksi keyword sederhana (buang stopwords & kata pendek)
     */
    private function extractKeywords($text)
    {
        $words = preg_split('/[^\w-]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = [
            'dan','atau','dengan','untuk','pada','di','ke','dari','ini','itu',
            'the','and','or','with','for','on','in','to','from','this','that',
            'a','an','is','are','was','were','be','been','being','have','has',
            'had','do','does','did','will','would','could','should','may','might',
            'must','can','as','at','by','if','into','of','off','onto','out','over',
            'toward','within','without'
        ];

        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        return array_unique($keywords);
    }

    /**
     * Skor kecocokan keyword
     */
    private function calculateKeywordMatch($contentKeywords, $fileNameKeywords)
    {
        if (empty($contentKeywords) || empty($fileNameKeywords)) {
            return 0;
        }

        $matchedKeywords = array_intersect($contentKeywords, $fileNameKeywords);
        $matchScore = (count($matchedKeywords) / max(1, count($contentKeywords))) * 100;

        return min(100, $matchScore);
    }

    /**
     * (opsional) Pemeriksaan "keaslian" file sederhana
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

        $validExtensions = ['pdf','jpg','jpeg','png','doc','docx','xls','xlsx','ppt','pptx'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $validExtensions)) {
            $authenticity['file_type_valid'] = true;
            $authenticity['verification_notes'][] = "Tipe file valid: {$fileExtension}";
        } else {
            $nativeFormats = [
                'application/vnd.google-apps.document',
                'application/vnd.google-apps.spreadsheet',
                'application/vnd.google-apps.presentation'
            ];
            if (in_array($file['mimeType'], $nativeFormats)) {
                $authenticity['file_type_valid'] = true;
                $authenticity['verification_notes'][] = "Format Google native: ".$file['mimeType'];
            } else {
                $authenticity['verification_notes'][] = "Tipe file tidak umum untuk sertifikat: {$fileExtension}";
            }
        }

        if (isset($file['size']) && $file['size'] !== null) {
            if ($file['size'] > 1024 && $file['size'] < 50 * 1024 * 1024) {
                $authenticity['size_check'] = true;
            } else {
                $authenticity['verification_notes'][] = "Ukuran file mencurigakan: " . $this->formatBytes($file['size']);
            }
        } else {
            $authenticity['size_check'] = true;
            $authenticity['verification_notes'][] = "Ukuran tidak tersedia (mungkin file Google native)";
        }

        if (strpos($file['mimeType'], 'pdf') !== false) {
            $authenticity['format_analysis']['is_document'] = true;
            $authenticity['format_analysis']['is_certified_format'] = true;
        } elseif (strpos($file['mimeType'], 'image') !== false) {
            $authenticity['format_analysis']['is_image'] = true;
        } elseif (in_array($file['mimeType'], [
            'application/vnd.google-apps.document',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ])) {
            $authenticity['format_analysis']['is_document'] = true;
        }

        $authenticity['content_match'] = isset($file['similarity_score']) && $file['similarity_score'] > 70;
        $authenticity['semantic_match'] = isset($file['semantic_score']) && $file['semantic_score'] > 60;

        $confidenceScore = 0;
        if ($authenticity['file_type_valid']) $confidenceScore += 25;
        if ($authenticity['size_check']) $confidenceScore += 15;
        if ($authenticity['content_match']) $confidenceScore += 30;
        if ($authenticity['semantic_match']) $confidenceScore += 30;
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
     * Format bytes menjadi teks human-readable
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B','KB','MB','GB','TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Generate expected file name dari isi prestasi
     */
    private function generateExpectedFileName($achievementLabel, $content)
    {
        $keywords = explode(' ', str_replace(['.', ',', '!', '?'], ' ', strtolower($content)));
        $relevantKeywords = array_slice(array_filter($keywords), 0, 3);
        $expectedName = $achievementLabel . ' ' . implode(' ', $relevantKeywords);

        return substr($expectedName, 0, 50) . '.pdf';
    }

    /**
     * Tipe file yang bisa diekstrak teksnya
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

        foreach ($textReadableTypes as $readableType) {
            if (strpos($mimeType, $readableType) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Placeholder (tidak terpakai di versi ini)
     */
    private function analyzeFileContentFromDrive($fileId, $achievement, $driveService = null)
    {
        return 0;
    }

    /**
     * Analisis konten (keyword vs konten)
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

            $foundKeywords = [];
            $totalKeywords = count($achievementKeywords);
            $matchedKeywords = 0;

            $firstPart = substr($fileContent, 0, 1000);
            $lastPart = substr($fileContent, -1000);
            $searchableContent = $firstPart . ' ' . $lastPart;

            foreach ($achievementKeywords as $keyword) {
                if (strlen($keyword) > 2 && stripos($searchableContent, $keyword) !== false) {
                    $foundKeywords[] = $keyword;
                    $matchedKeywords++;
                    if ($matchedKeywords > $totalKeywords * 0.3) {
                        break;
                    }
                }
            }

            $contentMatchScore = 0;
            if ($totalKeywords > 0) {
                $contentMatchScore = min(100, ($matchedKeywords / $totalKeywords) * 100);
            }

            $contentSimilarity = $this->fastSimilarityCheck(
                substr($fileContent, 0, 100),
                $achievementContent
            );

            // Gunakan OpenAI untuk analisis semantik yang lebih canggih jika API key tersedia
            $openaiSemanticScore = 0;
            if (!empty(env('OPENAI_API_KEY'))) {
                $openaiSemanticScore = $this->getOpenAISemanticScore($fileContent, $achievementContent);
            }

            $result['semantic_score'] = $openaiSemanticScore > 0
                ? ($contentMatchScore * 0.3 + $contentSimilarity * 0.2 + $openaiSemanticScore * 0.5)
                : ($contentMatchScore * 0.7 + $contentSimilarity * 0.3);
            $result['content_similarity'] = $contentSimilarity;
            $result['has_relevant_content'] = $contentMatchScore > 5;
            $result['relevant_keywords_found'] = $foundKeywords;
        } catch (\Exception $e) {
            $result['extraction_success'] = false;
            $result['semantic_score'] = 0;
            $result['content_similarity'] = 0;
        }

        return $result;
    }
    
    /**
     * Split text into sentences for better NLP processing
     */
    private function splitIntoSentences($text) {
        // Split on sentence delimiters
        $sentences = preg_split('/[.!?]+/', $text);
        return array_filter(array_map('trim', $sentences));
    }
    
    /**
     * Split text into chunks for processing
     */
    private function chunkText($text, $chunkSize) {
        $chunks = [];
        $textLength = strlen($text);
        
        for ($i = 0; $i < $textLength; $i += $chunkSize) {
            $chunks[] = substr($text, $i, $chunkSize);
        }
        
        return $chunks;
    }
    
    /**
     * Check if a keyword matches text using multiple techniques
     */
    private function isKeywordMatch($keyword, $text) {
        // Direct match
        if (strpos($text, $keyword) !== false) {
            return true;
        }
        
        // Word boundary match (to prevent substring matches)
        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $text)) {
            return true;
        }
        
        // Partial match with similarity threshold
        similar_text($keyword, $text, $percent);
        if ($percent > 80) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Calculate relevance score based on found keywords
     */
    private function calculateRelevanceScore($foundKeywords, $achievementContent) {
        if (empty($foundKeywords)) {
            return 0;
        }
        
        // Calculate score based on keyword importance
        $relevanceScore = 0;
        $achievementLower = strtolower($achievementContent);
        
        foreach ($foundKeywords as $keyword) {
            // Give higher score to keywords that appear in achievement content
            if (strpos($achievementLower, $keyword) !== false) {
                $relevanceScore += 15; // Higher weight for direct matches
            } else {
                $relevanceScore += 5; // Lower weight for other matches
            }
        }
        
        return min(100, $relevanceScore);
    }
    
    /**
     * Perform context-based matching between sentences and achievement content
     */
    private function contextBasedMatching($sentences, $achievementContent) {
        $matchScore = 0;
        $achievementWords = array_flip($this->extractKeywords($achievementContent));
        
        foreach ($sentences as $sentence) {
            // Extract keywords from sentence
            $sentenceKeywords = $this->extractKeywords($sentence);
            
            // Count matching keywords
            $matchingKeywords = array_intersect($sentenceKeywords, array_keys($achievementWords));
            
            if (count($matchingKeywords) > 0) {
                $matchScore += (count($matchingKeywords) / count($sentenceKeywords)) * 100;
            }
        }
        
        // Average the score across all sentences
        return count($sentences) > 0 ? min(100, $matchScore / count($sentences)) : 0;
    }

    /**
     * Mendapatkan skor semantik menggunakan OpenAI API
     */
    private function getOpenAISemanticScore($fileContent, $achievementContent)
    {
        try {
            $client = \OpenAI::client(env('OPENAI_API_KEY'));

            // Ambil sebagian konten untuk mengurangi biaya dan waktu
            $truncatedFileContent = substr($fileContent, 0, 3000);
            $truncatedAchievementContent = substr($achievementContent, 0, 800);

            $prompt = "Kamu adalah asisten yang ahli dalam memverifikasi kecocokan dokumen.
            Berikan penilaian dari 0-100 untuk seberapa relevan isi dokumen berikut dengan deskripsi prestasi berikut.
            Fokus pada kesesuaian konten, bukan hanya kesamaan kata.

            Isi Dokumen: {$truncatedFileContent}

            Deskripsi Prestasi: {$truncatedAchievementContent}

            Berikan hanya skor numerik antara 0-100 tanpa penjelasan tambahan:";

            $response = $client->chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 10,
                'temperature' => 0.1,
                'timeout' => 30
            ]);

            $result = $response->choices[0]->message->content;
            $score = (int) preg_replace('/[^\d]/', '', $result);

            // Pastikan skor dalam rentang 0-100
            return max(0, min(100, $score));
        } catch (\Exception $e) {
            \Log::error('OpenAI API error: ' . $e->getMessage());
            return 0; // Kembalikan 0 jika gagal, agar tidak menghentikan proses
        }
    }
}
