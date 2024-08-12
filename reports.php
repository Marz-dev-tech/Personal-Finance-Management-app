<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: main.php');
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Initialize variables
$transactions = [];
$budgets = [];

// Fetch all transactions
$sql = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY transaction_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    error_log("Error fetching transactions: " . $conn->error);
} else {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

// Fetch all budgets
$sql = "SELECT budgets.name, target_amount AS total_amount, COALESCE(SUM(amount), 0) AS amount_spent 
        FROM budgets 
        LEFT JOIN budget_transactions 
        ON budgets.id = budget_transactions.budget_id 
        WHERE budgets.user_id = '$user_id' 
        GROUP BY budgets.id";
$result = $conn->query($sql);

if ($result === false) {
    error_log("Error fetching budgets: " . $conn->error);
} else {
    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="homepage.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="360logo1.png" alt="logo">
        </div>
        <ul>
            <li><a href="homepage.php">Account</a></li>
            <li><a href="budgets.php">Budgets</a></li>
            <li><a href="#investments">Investments</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
        <button class="logout-btn">Logout</button>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top navigation bar -->
        <div class="navbar">
            <div class="user-info">
                <img src="defaultprofilepic.png" alt="User Icon" class="user-icon">
                <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </div>

        <!-- Reports content -->
        <div class="dashboard-content">
            <!-- Transactions Summary Table -->
            <div class="transaction-summary-container">
                <div class="transaction-summary">
                    <h2>All Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <button onclick="downloadCSV('transactions')">Download Transactions</button>
                </div>
            </div>

            <!-- Budgets Summary Table -->
            <div class="budget-summary-container">
                <div class="budget-summary">
                    <h2>Budgets Summary</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Budget Name</th>
                                <th>Total Amount</th>
                                <th>Amount Spent</th>
                                <th>Completion (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($budgets)): ?>
                                <?php foreach ($budgets as $budget): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($budget['name']); ?></td>
                                        <td><?php echo htmlspecialchars($budget['total_amount']); ?></td>
                                        <td><?php echo htmlspecialchars($budget['amount_spent']); ?></td>
                                        <td><?php echo round(($budget['amount_spent'] / $budget['total_amount']) * 100, 2); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No budgets found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <button onclick="downloadCSV('budgets')">Download Budgets</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function downloadCSV(type) {
        let filename = '';
        let rows = [];

        if (type === 'transactions') {
            filename = 'transactions_summary.csv';
            rows = [
                ["Amount", "Type", "Description", "Date"],
                <?php foreach ($transactions as $transaction): ?>
                    ["<?php echo htmlspecialchars($transaction['amount'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars($transaction['transaction_type'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars($transaction['transaction_date'], ENT_QUOTES, 'UTF-8'); ?>"],
                <?php endforeach; ?>
            ];
        } else if (type === 'budgets') {
            filename = 'budgets_summary.csv';
            rows = [
                ["Budget Name", "Total Amount", "Amount Spent", "Completion (%)"],
                <?php foreach ($budgets as $budget): ?>
                    ["<?php echo htmlspecialchars($budget['name'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars($budget['total_amount'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars($budget['amount_spent'], ENT_QUOTES, 'UTF-8'); ?>", "<?php echo round(($budget['amount_spent'] / $budget['total_amount']) * 100, 2); ?>%"],
                <?php endforeach; ?>
            ];
        }

        let csvContent = "data:text/csv;charset=utf-8," 
            + rows.map(e => e.join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", filename);
        document.body.appendChild(link); // Required for FF
        link.click();
    }

    document.querySelector('.logout-btn').addEventListener('click', function() {
    window.location.href = 'main.php';
});
    </script>
</body>
</html>
