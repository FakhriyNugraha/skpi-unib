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

        // Check if this is actually a file (not a folder)
        if ($mimeType === 'application/vnd.google-apps.folder') {
            error_log("Trying to extract text from a folder, not a file: " . $fileName);
            return '';
        }

        try {
            // Using the Google API Client to download the file content 
            // The proper way to download file content is by using the media download URL
            $url = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
            
            // Make the authorized request to download the file
            $client = $driveService->getClient();
            $response = $client->authorize()->request('GET', $url);
            
            $content = (string) $response->getBody();
            
            // Determine how to process based on MIME type
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (strpos($mimeType, 'pdf') !== false || $extension === 'pdf') {
                return self::extractTextFromPdfContent($content);
            } elseif (strpos($mimeType, 'document') !== false || in_array($extension, ['doc', 'docx'])) {
                // For document processing, we'll use the content directly without temp file
                return self::extractTextFromDocxContent($content);
            } elseif (strpos($mimeType, 'image') !== false || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                // For image processing, we still need temp file for OCR
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                if ($tempFile === false) {
                    error_log("Could not create temp file for image processing");
                    return '';
                }
                
                $result = file_put_contents($tempFile, $content);
                if ($result === false) {
                    error_log("Could not write image content to temp file: " . $tempFile);
                    return '';
                }
                
                try {
                    // Verify that tempFile is actually a file, not a directory
                    if (!is_file($tempFile)) {
                        error_log("Temp file is not a file: " . $tempFile);
                        return '';
                    }
                    
                    $result = self::extractTextFromImageContent($content, $tempFile);
                    return $result;
                } finally {
                    if (file_exists($tempFile) && is_file($tempFile)) {
                        unlink($tempFile);
                    }
                }
            } elseif (strpos($mimeType, 'text') !== false || $extension === 'txt') {
                return $content;
            } else {
                return $content;
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
            error_log("PDF parsing error: " . $e->getMessage());
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
        
        $result = file_put_contents($tempFile, $docxContent);
        if ($result === false) {
            return '';
        }
        
        try {
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
        $result = file_put_contents($tempFile, $imageContent);
        if ($result === false) {
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