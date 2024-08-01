<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo json_encode([]);
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];

// Fetch account balance data
$sql = "SELECT date, balance FROM account_balances WHERE user_id = '$user_id' AND date >= NOW() - INTERVAL 1 WEEK";
$result = $conn->query($sql);
$account_data = [];
while ($row = $result->fetch_assoc()) {
    $account_data[] = $row;
}

$conn->close();
echo json_encode($account_data);
?>
