<?php
require_once '../main/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /accounts/login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Optional: You can set a success message here if needed
    } else {
        // Optional: Log the error or set an error message
    }

    $stmt->close();
}

$conn->close();

header("Location: cart.php");
exit;
?>
