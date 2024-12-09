<?php
// Directly define the database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the delete query
    $query = "DELETE FROM medicines WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);  // "i" for integer
    if ($stmt->execute()) {
        header('Location: inventory.php'); // Redirect to inventory list
        exit();
    } else {
        echo "Error deleting medicine: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
