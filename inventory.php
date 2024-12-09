<?php
session_start();
include 'db.php';

// Low stock alert query
$low_stock_result = $conn->query("SELECT * FROM medicines WHERE stock < 10");

// Expiry date alert query
$expired_medicines_result = $conn->query("SELECT * FROM medicines WHERE expiry_date < NOW()");

// Fetch all medicines
$medicines_result = $conn->query("SELECT * FROM medicines");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dain App Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        /* Sidebar */
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
            margin: 10px 0;
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

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .sidebar .header {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: white;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed+.main {
            margin-left: 80px;
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
    <div class="sidebar" id="sidebar">

        <a href="/pharmacy_management/dashboard.php" class="active">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="/pharmacy_management/inventory.php">
            <i class="bi bi-box"></i>
            <span>Inventory</span>
        </a>

        <a href="#" onclick="toggleSales()"> <!-- Added onclick event -->
            <i class="bi bi-cash-stack"></i>
            <span>Sales</span>
        </a>

        <!-- Inventory List that will be toggled -->
        <ul class="list-unstyled ps-4" id="salesList" style="display: none;"> <!-- Initially hidden -->
            <li><a href="/pharmacy_management/sales.php">Sales</a></li>
            <li><a href="/pharmacy_management/generate_invoice.php">Generate Invoice</a></li>
            <li><a href="/pharmacy_management/add_customer.php">Add Customers</a></li>
        </ul>

        <a href="/pharmacy_management/user_management.php">
            <i class="bi bi-person"></i>
            <span>User Management</span>
        </a>

        <a href="#">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div>
            <div class="container mt-5">
                <h2 class="text-center mb-4">Inventory Management</h2>

                <?php
                if ($low_stock_result->num_rows > 0) {
                    echo "<div class='alert alert-warning'>Some medicines are low in stock.</div>";
                }

                if ($expired_medicines_result->num_rows > 0) {
                    echo "<div class='alert alert-danger'>Some medicines have expired.</div>";
                }
                ?>

                <!-- Search Bar -->
                <form action="inventory.php" method="GET" class="mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search Medicines..."
                        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                </form>

                <div class="text-left">
                    <a href="add_medicine.php" class="btn btn-success">Add New Medicine</a>
                </div><br>

                <!-- Inventory Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Manufacturer</th>
                            <th>Batch Number</th>
                            <th>Expiry Date</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Handle search query
                        $search_query = '';
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = $conn->real_escape_string($_GET['search']);
                            $search_query = " WHERE name LIKE '%$search%' OR category LIKE '%$search%' OR manufacturer LIKE '%$search%'";
                        }

                        // Fetch medicines with optional search filter
                        $medicines_query = "SELECT * FROM medicines" . $search_query;
                        $medicines_result = $conn->query($medicines_query);

                        // Loop through and display the medicine data
                        if ($medicines_result->num_rows > 0) {
                            while ($medicine = $medicines_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$medicine['id']}</td>";
                                echo "<td>{$medicine['name']}</td>";
                                echo "<td>{$medicine['category']}</td>";
                                echo "<td>{$medicine['manufacturer']}</td>";
                                echo "<td>{$medicine['batch_number']}</td>";
                                echo "<td>{$medicine['expiry_date']}</td>";
                                echo "<td>{$medicine['cost_price']}</td>";
                                echo "<td>{$medicine['selling_price']}</td>";
                                echo "<td>{$medicine['stock']}</td>";
                                echo "<td>
                                <a href='edit_medicine.php?id={$medicine['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='delete_medicine.php?id={$medicine['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this medicine?\")'>Delete</a>
                            </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No medicines found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        // Toggle Sales List
        function toggleSales() {
            const salesList = document.getElementById('salesList');
            if (salesList.style.display === "none") {
                salesList.style.display = "block"; // Show Sales list
            } else {
                salesList.style.display = "none"; // Hide Sales list
            }
        }


        // Income and Profit Chart
        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: 'Income ($)',
                        data: [500, 700, 600, 800],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Profit ($)',
                        data: [200, 300, 250, 400],
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // Most Diseases Chart
        const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
        const diseaseChart = new Chart(diseaseCtx, {
            type: 'pie',
            data: {
                labels: ['Flu', 'Diabetes', 'Hypertension', 'Asthma'],
                datasets: [
                    {
                        data: [40, 25, 20, 15],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)'
                        ],
                        hoverOffset: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    </script>
</body>