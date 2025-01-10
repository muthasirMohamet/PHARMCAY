<?php
$hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, password, role, is_active) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $username, $hashedPassword, $role, $isActive);

$username = 'admin';
$role = 'Admin';
$isActive = 1;
$stmt->execute();
?>
