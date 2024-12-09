<?php
include('../includes/db.php');

if (isset($_POST['search'])) {
    $search = $_POST['search'];

    $query = "SELECT * FROM medicines WHERE name LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Search Results</h3>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Name</th><th>Category</th><th>Manufacturer</th><th>Batch Number</th><th>Expiry Date</th><th>Cost Price</th><th>Selling Price</th><th>Stock</th><th>Action</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['category'] . "</td>";
        echo "<td>" . $row['manufacturer'] . "</td>";
        echo "<td>" . $row['batch_number'] . "</td>";
        echo "<td>" . $row['expiry_date'] . "</td>";
        echo "<td>" . $row['cost_price'] . "</td>";
        echo "<td>" . $row['selling_price'] . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        echo "<td>
                <a href='update_medicine.php?id=" . $row['id'] . "' class='btn btn-warning'>Update</a>
                <a href='delete_medicine.php?id=" . $row['id'] . "' class='btn btn-danger'>Delete</a>
              </td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    $stmt->close();
}

$conn->close();
?>

<!-- Search Form -->
<h3>Search Medicine</h3>
<form action="search_medicine.php" method="POST">
    <input type="text" name="search" placeholder="Search by name or category" required>
    <button type="submit">Search</button>
</form>
