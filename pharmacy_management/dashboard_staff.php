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
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
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

        /* Main Content Styles */
        .main-content {
            padding: 30px;
        }

        .widget {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard_staff.php" class="active"><i class="bi bi-house-door"></i><span>Dashboard</span></a>
        <a href="#" onclick="toggleSales()"><i class="bi bi-cash-stack"></i><span>Sales</span></a>
        <ul id="salesList" style="display: none;">
            <li><a href="sales.php">Sales</a></li>
            <li><a href="generate_invoice.php">Generate Invoice</a></li>
            <li><a href="add_customer.php">Add Customers</a></li>
        </ul>
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

            <div class="col-md-4">
                <div class="widget">
                    <h4>Total Sales (This Month)</h4>
                    <p>
                        <?php
                        // Calculate total sales for the current month
                        $result = $conn->query("SELECT SUM(final_price) AS total_sales FROM sales WHERE MONTH(sale_date) = MONTH(CURRENT_DATE())");
                        $total_sales = $result->fetch_assoc();
                        echo "$" . number_format($total_sales['total_sales'], 2);
                        ?>
                    </p>
                </div>
            </div>

            <!-- Sales Today Widget -->
            <div class="col-md-4">
                <div class="widget">
                    <h4>Sales Today</h4>
                    <p>
                        <?php
                        // Calculate sales for today
                        $result = $conn->query("SELECT SUM(final_price) AS sales_today FROM sales WHERE DATE(sale_date) = CURRENT_DATE()");
                        $sales_today = $result->fetch_assoc();
                        echo "$" . number_format($sales_today['sales_today'], 2);
                        ?>
                    </p>
                </div>
            </div>

            <!-- Number of Sales Widget -->
            <div class="col-md-4">
                <div class="widget">
                    <h4>Total Number of Sales</h4>
                    <p>
                        <?php
                        // Count total sales
                        $result = $conn->query("SELECT COUNT(*) AS total_sales_count FROM sales");
                        $sales_count = $result->fetch_assoc();
                        echo $sales_count['total_sales_count'];
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="table-responsive">
            <h3>Recent Sales</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Medicine Name</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch recent sales along with customer and medicine details (limit to 5)
                    $query = "SELECT sales.*, customers.name AS customer_name, medicines.name AS medicine_name FROM sales
                    JOIN customers ON sales.customer_id = customers.id
                    JOIN medicines ON sales.medicine_id = medicines.id
                    ORDER BY sale_date DESC LIMIT 5";
                    $sales = $conn->query($query);

                    // Loop through and display sales records
                    if ($sales->num_rows > 0) {
                        while ($sale = $sales->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$sale['id']}</td>";
                            echo "<td>{$sale['medicine_name']}</td>";
                            echo "<td>{$sale['customer_name']}</td>";  // Display customer name
                            echo "<td>$" . number_format($sale['final_price'], 2) . "</td>";  // Display sale amount (final price)
                            echo "<td>{$sale['sale_date']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No recent sales.</td></tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
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
<!-- #region -->