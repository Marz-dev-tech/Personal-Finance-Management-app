<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$budget_id = $_GET['budget_id'];
$user_id = $_SESSION['user_id'];

// Fetch budget details
$sql = "SELECT budgets.name, target_amount, COALESCE(SUM(amount), 0) as saved_amount FROM budgets LEFT JOIN budget_transactions ON budgets.id = budget_transactions.budget_id WHERE budgets.id = '$budget_id' AND budgets.user_id = '$user_id'";
$result = $conn->query($sql);
$budget = $result->fetch_assoc();

// Fetch transactions
$sql = "SELECT amount, date FROM budget_transactions WHERE budget_id = '$budget_id' ORDER BY date DESC";
$result = $conn->query($sql);
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode([
    'name' => $budget['name'],
    'target_amount' => $budget['target_amount'],
    'saved_amount' => $budget['saved_amount'],
    'transactions' => $transactions
]);

$conn->close();
?>
