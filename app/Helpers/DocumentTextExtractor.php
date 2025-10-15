<?php

namespace App\Helpers;

use Smalot\PdfParser\Parser as PdfParser;
use Google\Service\Drive;

if (!function_exists('assertReadableFile')) {
    /**
     * Assert that a path is a readable file, not a directory
     * 
     * @param string $path
     * @return string
     * @throws \RuntimeException
     */
    function assertReadableFile(string $path): string {
        if (is_dir($path)) throw new \RuntimeException("EISDIR: Path adalah direktori: $path");
        if (!is_file($path)) throw new \RuntimeException("Path bukan file reguler: $path");
        if (!is_readable($path)) throw new \RuntimeException("File tidak bisa dibaca: $path");
        return $path;
    }
}

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

        try {
            // Untuk Google Docs/Sheets/Slides: tidak bisa get alt=media langsung; harus diexport
            $isGoogleNative = in_array($originalMimeType, [
                'application/vnd.google-apps.document',
                'application/vnd.google-apps.spreadsheet', 
                'application/vnd.google-apps.presentation'
            ]);

            $content = '';
            
            if ($isGoogleNative) {
                // Export Google Docs/Sheets/Slides to PDF
                $bytes = $driveService->files->export($fileId, 'application/pdf')->getBody()->getContents();
                // Use PDF processing for exported content
                return self::extractTextFromPdfContent($bytes);
            } else {
                // Untuk file asli: gunakan alt=media
                $bytes = $driveService->files->get($fileId, ['alt'=>'media'])->getBody()->getContents();
                
                // Determine how to process based on original MIME type
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (strpos($originalMimeType, 'pdf') !== false) {
                    return self::extractTextFromPdfContent($bytes);
                } elseif (strpos($originalMimeType, 'document') !== false || in_array($extension, ['doc', 'docx'])) {
                    return self::extractTextFromDocxContent($bytes);
                } elseif (strpos($originalMimeType, 'image') !== false || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                    // Untuk gambar, kita kembalikan nama file sebagai referensi
                    return "Gambar file: " . $fileName;
                } elseif (strpos($originalMimeType, 'text') !== false || $extension === 'txt') {
                    // Untuk file teks, kita tetap bisa mengembalikan konten
                    return $bytes;
                } else {
                    // Untuk tipe file lain, kita kembalikan konten
                    return $bytes;
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
        try {
            // Validate that we have content
            if (empty($pdfContent)) {
                error_log("PDF content is empty");
                return '';
            }
            
            $parser = new PdfParser();
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

        // Validate that we have content
        if (empty($docxContent)) {
            error_log("DOCX content is empty");
            return '';
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        if ($tempFile === false) {
            error_log("Could not create temp file for DOCX processing");
            return '';
        }
        
        try {
            $result = file_put_contents($tempFile, $docxContent);
            if ($result === false) {
                error_log("Could not write DOCX content to temp file: " . $tempFile);
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
            // Safely remove temp file
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return '';
    }

    /**
     * Extract text from image content using OCR
     */
    private static function extractTextFromImageContent($imageContent, $tempFile)
    {
        // Di lingkungan produksi, kita hindari OCR untuk mencegah error
        // Kita hanya mengembalikan nama file sebagai indikator
        if (env('APP_ENV') === 'production' || env('APP_ENV') === 'prod') {
            return '';
        }
        
        // Untuk lingkungan pengembangan, kita bisa mencoba OCR jika tersedia
        try {
            // Validate that we have content
            if (empty($imageContent)) {
                error_log("Image content is empty");
                return '';
            }
            
            // Validate temp file
            if (empty($tempFile) || !file_exists($tempFile)) {
                error_log("Invalid temp file for OCR processing");
                return '';
            }
            
            // Verify that tempFile is actually a file, not a directory
            if (!is_file($tempFile)) {
                error_log("Image temp file is not a file: " . $tempFile);
                return '';
            }
            
            // Check if Tesseract is available
            if (self::isTesseractAvailable()) {
                $command = 'tesseract ' . escapeshellarg($tempFile) . ' stdout 2>/dev/null';
                $output = shell_exec($command);
                if ($output !== null) {
                    return $output;
                }
            }
        } catch (\Exception $e) {
            error_log("OCR processing error: " . $e->getMessage());
        }
        
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