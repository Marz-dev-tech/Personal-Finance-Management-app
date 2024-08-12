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

// Fetch budgets
$sql = "SELECT id, name, target_amount FROM budgets WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$budgets = [];
while ($row = $result->fetch_assoc()) {
    $budgets[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets</title>
    <link rel="stylesheet" href="budgets.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="360logo1.png" alt="logo">
        </div>
        <ul>
            <li><a href="homepage.php">Account</a></li>
            <li><a href="budgets.php" class="active">Budgets</a></li> <!-- This line ensures Budgets is active -->
            <li><a href="#investments">Investments</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
        <button class="logout-btn">Logout</button>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="navbar">
                <div class="user-info">
                    <img src="defaultprofilepic.png" alt="User Icon" class="user-icon">
                    <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                </div>
            </div>
        <h1>Budgets</h1>

        <!-- Budget content -->
        <div class="dashboard-content">
            <!-- New Budget Form Container -->
            <div class="container">
                <h2>Create New Budget</h2>
                <form id="budgetForm">
                    <label for="budget_name">Budget Name</label>
                    <input type="text" id="budget_name" name="budget_name" required>

                    <label for="target_amount">Target Amount</label>
                    <input type="number" id="target_amount" name="target_amount" required>

                    <button type="submit">Create Budget</button>
                </form>
            </div>

            <!-- Budgets Table Container -->
            <div class="container">
                <h2>Your Budgets</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Budget Name</th>
                            <th>Target Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($budgets as $budget): ?>
                            <tr data-budget-id="<?php echo $budget['id']; ?>">
                                <td><?php echo htmlspecialchars($budget['name']); ?></td>
                                <td><?php echo htmlspecialchars($budget['target_amount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Budget Details and Transactions Container -->
            <div class="container" data-budget-details style="display: none;">
                <h2 id="budgetName"></h2>
                <canvas id="budgetChart"></canvas>
                <!-- Add Transaction Form -->
                <div class="container">
                    <h2>Add Transaction</h2>
                    <form id="transactionForm">
                        <label for="transaction_amount">Amount</label>
                        <input type="number" id="transaction_amount" name="transaction_amount" required>
                        <label for="transaction_date">Date</label>
                        <input type="date" id="transaction_date" name="transaction_date" required>
                        <button type="submit">Add Transaction</button>
                    </form>
                </div>

                <!-- Transactions Table -->
                <div class="container">
                    <h2>Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <!-- Transactions will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="budgets.js"></script>
</body>
</html>
