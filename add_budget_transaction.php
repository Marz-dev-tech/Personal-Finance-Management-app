<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$budget_id = $_POST['budget_id'];
$amount = $_POST['transaction_amount'];
$date = $_POST['transaction_date'];
$user_id = $_SESSION['user_id'];

// Validate input
if (!is_numeric($amount) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Insert transaction into the database
$sql = "INSERT INTO budget_transactions (budget_id, amount, date, user_id) VALUES ('$budget_id', '$amount', '$date', '$user_id')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>
