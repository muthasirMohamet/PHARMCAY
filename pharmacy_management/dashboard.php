<?php

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


// Include the database connection file
include 'db.php';

// Query to count the total number of customers
$query = "SELECT COUNT(id) AS total_customers FROM customers";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_customers = $row['total_customers'];

// Query to calculate the total sales
$query1 = "SELECT SUM(final_price) AS total_sales FROM sales";
$result1 = $conn->query($query1);
$row1 = $result1->fetch_assoc();
$total_sales = $row1['total_sales'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #2c3e50;
            padding-top: 20px;
            transition: width 0.3s;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
            gap: 15px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #d35400;
        }

        .sidebar i {
            font-size: 1.5rem;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .widget {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php" class="active"><i class="bi bi-house-door"></i><span>Dashboard</span></a>
        <a href="inventory.php"><i class="bi bi-box"></i><span>Inventory</span></a>
        <a href="#" onclick="toggleSales()"><i class="bi bi-cash-stack"></i><span>Sales</span></a>
        <ul id="salesList" style="display: none;">
            <li><a href="sales.php">Sales</a></li>
            <li><a href="generate_invoice.php">Generate Invoice</a></li>
            <li><a href="add_customer.php">Add Customers</a></li>
        </ul>
        <a href="user_management.php"><i class="bi bi-person"></i><span>User Management</span></a>
        <a href="#"><i class="bi bi-gear"></i><span>Settings</span></a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h1 class="mb-4">Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="widget">
                    <h4>Total Customers</h4>
                    <p><?php echo $total_customers; ?> People</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <h4>Total Sales</h4>
                    <p>$<?php echo number_format($total_sales, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="widget">
                    <h4>Income and Profit</h4>
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <h4>Most Common Diseases</h4>
                    <canvas id="diseaseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle Sales List
        function toggleSales() {
            const salesList = document.getElementById('salesList');
            salesList.style.display = salesList.style.display === 'none' ? 'block' : 'none';
        }

        // Income and Profit Chart
        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    { label: 'Income', data: [500, 700, 600, 800], backgroundColor: 'rgba(75, 192, 192, 0.7)' },
                    { label: 'Profit', data: [200, 300, 250, 400], backgroundColor: 'rgba(255, 159, 64, 0.7)' }
                ]
            },
            options: { responsive: true }
        });

        // Diseases Chart
        const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
        new Chart(diseaseCtx, {
            type: 'pie',
            data: {
                labels: ['Flu', 'Diabetes', 'Hypertension', 'Asthma'],
                datasets: [
                    { data: [40, 25, 20, 15], backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0'] }
                ]
            },
            options: { responsive: true }
        });
    </script>
</body>

</html>
