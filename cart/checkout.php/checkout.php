<?php
session_start();
require_once '../main/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /accounts/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order placed! Please pay via M-Pesa Till Number: 8253176";
    } else {
        $_SESSION['message'] = "There was an error placing your order. Please try again.";
    }
    $stmt->close();

    header("Location: /");
    exit;
}

$stmt = $conn->prepare("SELECT ci.quantity, p.name, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Checkout - Deals by Keith</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/css/style.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <!-- Navbar content here -->
</nav>
<div class="container mt-4">
    <h2>Checkout</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                while ($item = $result->fetch_assoc()):
                    $line_total = $item['price'] * $item['quantity'];
                    $grand_total += $line_total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td>KSh <?= number_format($item['price'], 2) ?></td>
                    <td>KSh <?= number_format($line_total, 2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <th colspan="3" class="text-end">Grand Total:</th>
                    <th>KSh <?= number_format($grand_total, 2) ?></th>
                </tr>
            </tbody>
        </table>
        <form method="post" onsubmit="this.querySelector('button').disabled=true;">
            <p>Please pay via M-Pesa Till Number: <strong>8253176</strong></p>
            <button type="submit" class="btn btn-primary">Confirm Order</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
