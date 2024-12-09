<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM medicines WHERE id = $id");
    $medicine = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $manufacturer = $_POST['manufacturer'];
    $batch_number = $_POST['batch_number'];
    $expiry_date = $_POST['expiry_date'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE medicines SET name=?, category=?, manufacturer=?, batch_number=?, expiry_date=?, cost_price=?, selling_price=?, stock=? WHERE id=?");
    $stmt->bind_param("ssssssdis", $name, $category, $manufacturer, $batch_number, $expiry_date, $cost_price, $selling_price, $stock, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Medicine updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating medicine.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Medicine</h3>
        <form action="edit_medicine.php?id=<?php echo $medicine['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Medicine Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $medicine['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo $medicine['category']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="manufacturer" class="form-label">Manufacturer</label>
                <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?php echo $medicine['manufacturer']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="batch_number" class="form-label">Batch Number</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" value="<?php echo $medicine['batch_number']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo $medicine['expiry_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="cost_price" class="form-label">Cost Price</label>
                <input type="number" class="form-control" id="cost_price" name="cost_price" value="<?php echo $medicine['cost_price']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="selling_price" class="form-label">Selling Price</label>
                <input type="number" class="form-control" id="selling_price" name="selling_price" value="<?php echo $medicine['selling_price']; ?>" required>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $medicine['stock']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Medicine</button>
        </form>
    </div>
</body>
</html>
    