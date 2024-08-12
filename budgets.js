document.addEventListener('DOMContentLoaded', function() {
    const budgetRows = document.querySelectorAll('tbody tr');//('.budgets-table tbody tr')
    const budgetDetails = document.querySelector('.budget-details');//('.budget-details');
    const budgetChartCanvas = document.getElementById('budgetChart').getContext('2d');
    let currentBudgetId = null;
    let budgetChart = null;

    budgetRows.forEach(row => {
        row.addEventListener('click', function() {
            currentBudgetId = this.getAttribute('data-budget-id');
            loadBudgetDetails(currentBudgetId);
        });
    });

    document.getElementById('budgetForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('add_budget.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload(); // Reload the page to show the new budget
            } else {
                alert(data.message);
            }
        });
    });

    document.getElementById('transactionForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('budget_id', currentBudgetId);
        fetch('add_transaction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadBudgetDetails(currentBudgetId); // Reload the budget details to reflect the new transaction
            } else {
                alert(data.message);
            }
        });
    });

    function loadBudgetDetails(budgetId) {
        fetch(`get_budget_details.php?budget_id=${budgetId}`)
            .then(response => response.json())
            .then(data => {
                const { target_amount, saved_amount, transactions } = data;

                budgetDetails.style.display = 'block';

                if (budgetChart) {
                    budgetChart.destroy();
                }

                budgetChart = new Chart(budgetChartCanvas, {
                    type: 'pie',
                    data: {
                        labels: ['Saved', 'Remaining'],
                        datasets: [{
                            data: [saved_amount, target_amount - saved_amount],
                            backgroundColor: ['green', 'red']
                        }]
                    }
                });

                const transactionsTableBody = document.getElementById('transactionsTableBody');
                transactionsTableBody.innerHTML = '';
                transactions.forEach(transaction => {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${transaction.amount}</td><td>${transaction.date}</td>`;
                    transactionsTableBody.appendChild(row);
                });
            });
    }
});
