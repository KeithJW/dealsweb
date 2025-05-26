<?php
require_once '../main/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: /accounts/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];

// Validate product_id
if (!is_numeric($product_id)) {
    die("Invalid product ID.");
}

// Check if item already exists
$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();

if ($existing) {
    $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?");
    $stmt->bind_param("i", $existing['id']);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

header("Location: cart.php");
exit;
?>
