<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$budget_name = $_POST['budget_name'];
$target_amount = $_POST['target_amount'];

$sql = "INSERT INTO budgets (user_id, name, target_amount) VALUES ('$user_id', '$budget_name', '$target_amount')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error creating budget']);
}

$conn->close();
?>
