<?php
session_start();
include 'connect.php';
require 'vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: main.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['statement'])) {
    $file = $_FILES['statement'];

    // Check for upload errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileType = pathinfo($filename, PATHINFO_EXTENSION);

        // Processing CSV or Excel files
        if ($fileType == 'csv' || $fileType == 'xlsx') {
            if ($fileType == 'csv') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            } else {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            }

            $spreadsheet = $reader->load($fileTmpPath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            foreach ($sheetData as $row) {
                // columns: Date, Description, Amount, Type
                if (empty($row[0]) || !is_numeric($row[2])) {
                    continue; // Skip rows with missing or invalid data
                }
                $transaction_date = date('Y-m-d', strtotime($row[0]));
                $description = $conn->real_escape_string($row[1]);
                $amount = (float) $row[2];
                $transaction_type = $conn->real_escape_string($row[3]);

                $sql = "INSERT INTO transactions (user_id, transaction_date, description, amount, transaction_type) 
                        VALUES ('$user_id', '$transaction_date', '$description', '$amount', '$transaction_type')";

                if ($conn->query($sql) === false) {
                    error_log("Error inserting transaction: " . $conn->error . " | SQL: " . $sql);
                }
            }

            header('Location: homepage.php');
            exit; // Ensure script execution stops after redirect
        } else {
            echo "Unsupported file type.";
        }
    } else {
        echo "Error uploading file.";
    }
}

$conn->close();
