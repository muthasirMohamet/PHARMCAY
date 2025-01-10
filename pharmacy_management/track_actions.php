<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. Admin only.";
    exit();
}

$actions = $pdo->query("SELECT * FROM user_actions")->fetchAll();

echo "<table>";
echo "<tr><th>Action ID</th><th>Action</th><th>User</th><th>Date</th></tr>";

foreach ($actions as $action) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
    $stmt->execute(['id' => $action['user_id']]);
    $user = $stmt->fetch();
    
    echo "<tr>";
    echo "<td>{$action['id']}</td>";
    echo "<td>{$action['action']}</td>";
    echo "<td>{$user['username']}</td>";
    echo "<td>{$action['action_time']}</td>";
    echo "</tr>";
}

echo "</table>";
?>
