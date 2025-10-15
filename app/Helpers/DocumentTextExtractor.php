<?php

namespace App\Helpers;

use Smalot\PdfParser\Parser as PdfParser;
use Google\Service\Drive;

class DocumentTextExtractor
{
    /**
     * Extract text content from Google Drive file
     * 
     * @param object $driveFile Google Drive file object
     * @param Drive $driveService Google Drive service instance
     * @return string Extracted text content
     */
    public static function extractTextFromDriveFile($driveFile, $driveService)
    {
        $fileId = $driveFile->getId();
        $originalMimeType = $driveFile->getMimeType();
        $fileName = $driveFile->getName();

        // Abaikan folder: mimeType !== 'application/vnd.google-apps.folder'
        if ($originalMimeType === 'application/vnd.google-apps.folder') {
            error_log("Skipping folder: " . $fileName);
            return '';
        }

        // Untuk lingkungan produksi/cloud, kita gunakan pendekatan yang lebih aman
        // Cek apakah ini lingkungan produksi (bisa disesuaikan dengan environment variable)
        $isProduction = env('APP_ENV') === 'production' || env('APP_ENV') === 'prod';

        // Di lingkungan produksi, kita nonaktifkan ekstraksi konten untuk mencegah error
        if ($isProduction) {
            // Kembalikan nama file sebagai indikator bahwa file tersedia
            // Sistem akan menggunakan pencocokan nama file saja
            return "File tersedia: " . $fileName;
        }

        try {
            // Untuk Google Docs/Sheets/Slides: tidak bisa get alt=media langsung; harus diexport
            $isGoogleNative = in_array($originalMimeType, [
                'application/vnd.google-apps.document',
                'application/vnd.google-apps.spreadsheet', 
                'application/vnd.google-apps.presentation'
            ]);

            $content = '';
            
            if ($isGoogleNative) {
                // Get export links for Google native files
                $file = $driveService->files->get($fileId, array('fields' => 'exportLinks'));
                
                if ($file->getExportLinks() && isset($file->getExportLinks()['application/pdf'])) {
                    // Export Google Docs/Sheets/Slides to PDF
                    $exportUrl = $file->getExportLinks()['application/pdf'];
                    
                    $client = $driveService->getClient();
                    $response = $client->authorize()->request('GET', $exportUrl);
                    $content = (string) $response->getBody();
                    // Use PDF processing for exported content
                    return self::extractTextFromPdfContent($content);
                } else {
                    error_log("No export link available for Google file: " . $fileName);
                    return '';
                }
            } else {
                // Untuk file asli: gunakan alt=media
                $url = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
                
                $client = $driveService->getClient();
                $response = $client->authorize()->request('GET', $url);
                $content = (string) $response->getBody();
                
                // Determine how to process based on original MIME type
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (strpos($originalMimeType, 'pdf') !== false) {
                    return self::extractTextFromPdfContent($content);
                } elseif (strpos($originalMimeType, 'document') !== false || in_array($extension, ['doc', 'docx'])) {
                    return self::extractTextFromDocxContent($content);
                } elseif (strpos($originalMimeType, 'image') !== false || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                    // Untuk lingkungan cloud, kita hindari OCR untuk sementara karena bisa menyebabkan error
                    // Kita kembalikan nama file sebagai referensi saja
                    // Untuk sekarang, kita hanya kembalikan konten untuk pencocokan nama
                    return "Gambar file: " . $fileName . " (konten tidak dapat diekstrak untuk OCR di lingkungan cloud)";
                } elseif (strpos($originalMimeType, 'text') !== false || $extension === 'txt') {
                    // Untuk file teks, kita tetap bisa mengembalikan konten
                    return $content;
                } else {
                    // Untuk tipe file lain, kita kembalikan konten atau nama file sebagai indikator
                    return $content;
                }
            }
        } catch (\Exception $e) {
            error_log("DocumentTextExtractor error: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Extract text from PDF content
     */
    private static function extractTextFromPdfContent($pdfContent)
    {
        $parser = new PdfParser();
        try {
            // Using parseContent method which accepts the content directly as string
            $pdf = $parser->parseContent($pdfContent);
            return $pdf->getText();
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            error_log("PDF parsing error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return '';
        }
    }

    /**
     * Extract text from Word document content
     */
    private static function extractTextFromWordContent($wordContent, $tempFile)
    {
        // This function is not used anymore since we changed the approach
        // but keeping it for compatibility with existing code
        return self::extractTextFromDocxContent($wordContent);
    }

    /**
     * Extract text from Word document content using zip extraction
     */
    private static function extractTextFromDocxContent($docxContent)
    {
        if (!extension_loaded('zip')) {
            return '';
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        if ($tempFile === false) {
            return '';
        }
        
        try {
            $result = file_put_contents($tempFile, $docxContent);
            if ($result === false) {
                return '';
            }
            
            // Verify that tempFile is actually a file, not a directory
            if (!is_file($tempFile)) {
                error_log("DOCX temp file is not a file: " . $tempFile);
                return '';
            }
            
            $zip = new \ZipArchive();
            $res = $zip->open($tempFile);
            if ($res === true) {
                $content = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($content) {
                    // Remove XML tags and decode entities
                    $content = strip_tags($content);
                    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                    return $content;
                }
            } else {
                // Log the specific error for debugging
                error_log("DOCX parsing error: ZipArchive::open returned error code: " . $res);
            }
        } catch (\Exception $e) {
            error_log("DOCX parsing error: " . $e->getMessage());
        } finally {
            if (file_exists($tempFile) && is_file($tempFile)) {
                unlink($tempFile);
            }
        }

        return '';
    }

    /**
     * Extract text from image content using OCR
     */
    private static function extractTextFromImageContent($imageContent, $tempFile)
    {
        // Di lingkungan cloud, kita hindari operasi yang kompleks untuk menghindari error
        // Fungsi ini sekarang kita kosongkan untuk mencegah error di lingkungan produksi
        return '';
    }

    /**
     * Check if Tesseract OCR is available
     */
    private static function isTesseractAvailable()
    {
        $result = shell_exec('tesseract --version 2>&1');
        return $result !== null && strpos($result, 'tesseract') !== false;
    }
}