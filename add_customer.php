<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];

    // Insert customer details into the database
    $stmt = $conn->prepare("INSERT INTO customers (name, contact) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $contact);
    $stmt->execute();
    $stmt->close();

    echo "Customer added successfully!";
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: add_customer.php"); // Redirect to refresh the page
    exit();
}

// Fetch customer details
$result = $conn->query("SELECT id, name, contact FROM customers ORDER BY id DESC");
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
            /* Make the sidebar fill the full height of the viewport */
            position: fixed;
            /* Fix the sidebar to the left */
            top: 0;
            /* Make sure it starts at the top */
            left: 0;
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
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
        <div class="container mt-5">
            <h2 class="text-center">Add Customer</h2>
            <form method="POST" action="add_customer.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Customer Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </form>

            <h3 class="mt-5">Customer List</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td>
                                <a href="edit_customer.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <a href="add_customer.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

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