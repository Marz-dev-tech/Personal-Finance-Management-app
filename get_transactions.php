<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo '';
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];

// Fetch transactions
$sql = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY transaction_date DESC LIMIT 5";
$result = $conn->query($sql);

$rows = '';
while ($transaction = $result->fetch_assoc()) {
    $rows .= '<tr>
                <td>' . htmlspecialchars($transaction['transaction_type']) . '</td>
                <td>' . htmlspecialchars($transaction['description']) . '</td>
                <td>' . htmlspecialchars($transaction['transaction_date']) . '</td>
              </tr>';
}

$conn->close();
echo $rows;
?>
