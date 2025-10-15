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
        $mimeType = $driveFile->getMimeType();
        $fileName = $driveFile->getName();

        // Download file content to temporary location
        $tempFile = tempnam(sys_get_temp_dir(), 'drive_file_');
        
        try {
            // Using the Google API Client to download the file content 
            // The proper way to download file content is by using the media download URL
            $url = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
            
            // Make the authorized request to download the file
            $client = $driveService->getClient();
            $response = $client->authorize()->request('GET', $url);
            
            $content = (string) $response->getBody();
            
            file_put_contents($tempFile, $content);
            
            // Determine how to process based on MIME type
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (strpos($mimeType, 'pdf') !== false || $extension === 'pdf') {
                return self::extractTextFromPdfContent($content);
            } elseif (strpos($mimeType, 'document') !== false || in_array($extension, ['doc', 'docx'])) {
                return self::extractTextFromWordContent($content, $tempFile);
            } elseif (strpos($mimeType, 'image') !== false || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                return self::extractTextFromImageContent($content, $tempFile);
            } elseif (strpos($mimeType, 'text') !== false || $extension === 'txt') {
                return $content;
            } else {
                return $content;
            }
        } catch (\Exception $e) {
            error_log("DocumentTextExtractor error: " . $e->getMessage());
            return '';
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Extract text from PDF content
     */
    private static function extractTextFromPdfContent($pdfContent)
    {
        $parser = new PdfParser();
        try {
            $pdf = $parser->parseContent($pdfContent);
            return $pdf->getText();
        } catch (\Exception $e) {
            error_log("PDF parsing error: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Extract text from Word document content
     */
    private static function extractTextFromWordContent($wordContent, $tempFile)
    {
        // Save content to temp file for processing
        file_put_contents($tempFile, $wordContent);
        
        $extension = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
        
        if ($extension === 'doc') {
            // For .doc files, we might need antiword or similar utility
            // Here we're implementing the zip-based approach for .docx, 
            // but we'll need to handle .doc differently
            return self::extractTextFromDocxContent($wordContent);
        } else {
            return self::extractTextFromDocxContent($wordContent);
        }
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
        file_put_contents($tempFile, $docxContent);
        
        try {
            $zip = new \ZipArchive();
            if ($zip->open($tempFile) === true) {
                $content = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($content) {
                    // Remove XML tags and decode entities
                    $content = strip_tags($content);
                    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                    return $content;
                }
            }
        } catch (\Exception $e) {
            error_log("DOCX parsing error: " . $e->getMessage());
        } finally {
            if (file_exists($tempFile)) {
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
        // Save content to temp file for OCR processing
        file_put_contents($tempFile, $imageContent);
        
        // Check if Tesseract is available
        if (self::isTesseractAvailable()) {
            $command = 'tesseract ' . escapeshellarg($tempFile) . ' stdout 2>/dev/null';
            $output = shell_exec($command);
            if ($output !== null) {
                return $output;
            }
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