<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class ReceiptService
{

    public function __construct(
        private Database $db
    ) {
    }

    public function validateFile(?array $file)
    {
        if (!$file || $file['size'] === 0 || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException(['receipt' => "File upload failed..."]);
        }

        $maxFileSizeMB = 1.6 * 1024 * 1024;

        if ($file['size'] > $maxFileSizeMB) {
            throw new ValidationException(['receipt' => 'File too large to upload']);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-Za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationException(['receipt' => 'Invalid characters in file name.']);
        }

        $clientMimeType = $file['type'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array($clientMimeType, $allowedMimeTypes)) {
            throw new ValidationException(["receipt" => "Invalid file type {$clientMimeType}"]);
        }
    }

    public function upload(array $file)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = bin2hex(random_bytes(16)) . "." . $fileExtension;

        dd($newFilename);
    }
}
