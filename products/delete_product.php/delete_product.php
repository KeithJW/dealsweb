<?php
require_once '../main/config.php';

// Check if admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: /accounts/login.php");
    exit;
}

// Validate and sanitize input
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Deletion successful
        header("Location: admin_products.php?status=deleted");
    } else {
        // Deletion failed
        header("Location: admin_products.php?status=error");
    }

    $stmt->close();
} else {
    // Invalid or missing id
    header("Location: admin_products.php?status=invalid");
}

$conn->close();
?>
