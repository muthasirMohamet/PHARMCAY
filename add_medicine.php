<?php

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $manufacturer = $_POST['manufacturer'];
    $batch_number = $_POST['batch_number'];
    $expiry_date = $_POST['expiry_date'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];

    // Insert new medicine into database
    $query = "INSERT INTO medicines (name, category, manufacturer, batch_number, expiry_date, cost_price, selling_price, stock)
              VALUES ('$name', '$category', '$manufacturer', '$batch_number', '$expiry_date', '$cost_price', '$selling_price', '$stock')";
    if ($conn->query($query) === TRUE) {
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add New Medicine</h2>
        <button type="submit" class="btn btn-success margin-left">Add Medicine</button>
        <button type="" class="btn btn-success"><a href="/pharmacy_management/inventory.php">Back</a></button><br>

        <form action="add_medicine.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Medicine Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="mb-3">
                <label for="manufacturer" class="form-label">Manufacturer</label>
                <input type="text" class="form-control" id="manufacturer" name="manufacturer" required>
            </div>
            <div class="mb-3">
                <label for="batch_number" class="form-label">Batch Number</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" required>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
            </div>
            <div class="mb-3">
                <label for="cost_price" class="form-label">Cost Price</label>
                <input type="number" class="form-control" id="cost_price" name="cost_price" required>
            </div>
            <div class="mb-3">
                <label for="selling_price" class="form-label">Selling Price</label>
                <input type="number" class="form-control" id="selling_price" name="selling_price" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>