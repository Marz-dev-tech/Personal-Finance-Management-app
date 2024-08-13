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
$result = null;
$transactions = null;
$account_data = [];

// Fetch account balance data
$sql = "SELECT date, balance FROM account_balances WHERE user_id = '$user_id' AND date >= NOW() - INTERVAL 1 WEEK";
$result = $conn->query($sql);

if ($result === false) {
    error_log("Error fetching account balance data: " . $conn->error);
} else {
    while ($row = $result->fetch_assoc()) {
        $account_data[] = $row;
    }
}

// Fetch transactions
$sql = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY transaction_date DESC LIMIT 5";
$transactions = $conn->query($sql);

if ($transactions === false) {
    error_log("Error fetching transactions: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="homepage.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="360logo1.png" alt="logo">
        </div>
        <ul>
            <li><a href="#account" class="account-btn">Account</a></li>
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

        <!-- Dashboard content -->
        <div class="dashboard-content">
            <!-- Line graph -->
            <div class="graph-container">
                <canvas id="lineGraph"></canvas>
            </div>

            <!-- Upload File Button -->
            <div class="upload-container">
                <button id="uploadFileBtn">Upload File</button>
            </div>

            <!-- File Upload Form (Hidden by default) -->
            <div id="uploadFileForm" style="display: none;">
                <h2>Upload Bank Statement</h2>
                <form action="upload_statement.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="statement" accept=".pdf, .csv, .xlsx" required>
                    <button type="submit">Upload</button>
                </form>
            </div>

            <!-- New Transactions Form (Hidden by default, dropdown-triggered) -->
            <div class="new-transactions-form" id="newTransactionForm" style="display: none;">
                <h2>New Transactions</h2>
                <form id="transactionForm">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" required>

                    <label for="transaction_type">Transaction Type</label>
                    <select id="transaction_type" name="transaction_type" required>
                       <<option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                    </select>

                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required>

                    <label for="transaction_date">Transaction Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" required>

                    <button type="submit">Add Transaction</button>
                </form>
            </div>

            <!-- Transaction summary -->
            <div class="transaction-summary-container">
                <div class="transaction-summary">
                    <h2>Recent Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody">
                            <?php if ($transactions && $transactions->num_rows > 0): ?>
                                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No recent transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
      // Declare chartInstance in the global scope
let chartInstance = null;

// Prepare data for the line graph
function initializeGraph() {
  
    const accountData = <?php echo json_encode($account_data); ?>;
    const labels = accountData.map(data => data.date);
    const dataPoints = accountData.map(data => data.balance);

    const ctx = document.getElementById('lineGraph').getContext('2d');

    // Destroy the previous chart instance if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Create a new chart instance
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Account Balance',
                data: dataPoints,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateGraph() {
    fetch('get_account_data.php') // Fetch updated account data
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(accountData => {
            if (!Array.isArray(accountData)) {
                throw new Error('Unexpected data format');
            }

            const labels = accountData.map(data => data.date);
            const dataPoints = accountData.map(data => data.balance);

            const ctx = document.getElementById('lineGraph').getContext('2d');

            // Destroy the previous chart instance if it exists
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Create a new chart instance
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Account Balance Over the Last Week',
                        data: dataPoints,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching account data:', error);
        });
}

function updateTransactionTable() {
    fetch('get_transactions.php') // Fetch updated transactions
        .then(response => response.text())
        .then(html => {
            document.getElementById('transactionTableBody').innerHTML = html;
        });
}

// Initial load of graph and transactions
window.onload = function() {
    initializeGraph();  // Initial graph creation
    updateTransactionTable();
};

// Toggle File Upload Form
document.getElementById('uploadFileBtn').addEventListener('click', function() {
    const uploadForm = document.getElementById('uploadFileForm');
    uploadForm.style.display = uploadForm.style.display === 'block' ? 'none' : 'block';
});

// Toggle New Transactions Form
document.querySelector('.account-btn').addEventListener('click', function() {
    const form = document.getElementById('newTransactionForm');
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
});

// Handle New Transaction Form Submission
document.getElementById('transactionForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('add_transaction.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            // Reload the page to update the graph and table
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
});

// Handle logout
document.querySelector('.logout-btn').addEventListener('click', function() {
    window.location.href = 'main.php';
});

    </script>
</body>
</html>
