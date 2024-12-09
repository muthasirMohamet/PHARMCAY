<?php
session_start();
include 'db.php';

// Hubi haddii user-ku uu yahay staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'SalesStaff') {
    header("Location: login.php"); // Dib ugu celi bogga login haddii aan staff ahayn
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .container {
            padding: 20px;
        }
        .sales-form {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .sales-form input, .sales-form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .sales-form button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Staff Dashboard - Sales</h1>
    </div>
    <div class="container">
        <h2>Make a Sale</h2>
        <form class="sales-form" action="process_sales.php" method="POST">
            <label for="product">Product Name:</label>
            <input type="text" id="product" name="product" placeholder="Enter product name" required>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required>
            
            <label for="price">Price per Unit:</label>
            <input type="number" id="price" name="price" placeholder="Enter price per unit" step="0.01" required>
            
            <button type="submit">Submit Sale</button>
        </form>
    </div>
</body>
</html>
