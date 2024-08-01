<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    $response = array('status' => 'error', 'message' => 'User not logged in');
    echo json_encode($response);
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $amount = $conn->real_escape_string($_POST['amount']);
    $type = $conn->real_escape_string($_POST['transaction_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $transaction_date = $conn->real_escape_string($_POST['transaction_date']);

    // Insert data into transactions table
    $sql = "INSERT INTO transactions (user_id, amount, transaction_type, description, transaction_date) 
            VALUES ('$user_id', '$amount', '$type', '$description', '$transaction_date')";

    if ($conn->query($sql) === TRUE) {
        // Update account balance
        $sql_balance = "INSERT INTO account_balances (user_id, date, balance) 
                        SELECT '$user_id', '$transaction_date', 
                        (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = '$user_id') 
                        FROM DUAL 
                        ON DUPLICATE KEY UPDATE balance = (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = '$user_id')";

        if ($conn->query($sql_balance) === TRUE) {
            $response = array('status' => 'success', 'message' => 'Transaction added and balance updated successfully');
        } else {
            $response = array('status' => 'error', 'message' => 'Error updating balance: ' . $conn->error);
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Error: ' . $conn->error);
    }

    $conn->close();
    echo json_encode($response);
}
?>
