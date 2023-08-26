<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

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

    public function upload(array $file, $transaction_id)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = bin2hex(random_bytes(16)) . "." . $fileExtension;

        $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ValidationException(['receipt' => "Failed to upload file."]);
        };

        $this->db->query(
            "INSERT INTO receipts (
                original_filename,
                storage_filename,
                media_type,
                transaction_id    
            ) VALUES(
                :originalFilename,
                :storageFilename,
                :mediaType,
                :transactionId
            )",
            [
                'originalFilename' => $file['name'],
                'storageFilename' => $newFilename,
                'mediaType' => $file['type'],
                'transactionId' => $transaction_id
            ]
        );
    }

    public function getReceipt($receipt_id)
    {

        $receipt = $this->db->query(
            "SELECT * FROM receipts
            WHERE id = :receipt_id ",
            ['receipt_id' => $receipt_id]
        )->find();

        return $receipt;
    }

    public function read(array $receipt)
    {
        $filepath = PATHS::STORAGE_UPLOADS . "/" . $receipt['storage_filename'];

        if (!file_exists($filepath)) {
            redirectTo('/');
        }

        $doctype = $receipt['media_type'] === 'pdf' ? 'application/pdf' : $receipt['media_type'];

        header("Content-Disposition: inline;filename={$receipt['original_filename']}");
        header("Content-Type: {$doctype}");

        readfile($filepath);
    }

    public function delete(array $receipt)
    {
        $filepath = PATHS::STORAGE_UPLOADS . "/" . $receipt['storage_filename'];

        if (!file_exists($filepath)) {
            redirectTo('/');
        }

        if (!unlink($filepath)) {
            redirectTo('/');
        }

        $this->db->query(
            "DELETE FROM receipts
            WHERE id = :receipt_id",
            ['receipt_id' => $receipt['id']]
        );
    }
}