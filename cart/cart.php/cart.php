<?php
session_start();
require_once '../main/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: /accounts/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT ci.*, p.name, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- head same as before -->
</head>
<body>
    <!-- navbar same as before -->
    <div class="container mt-4">
        <h2>Your Cart</h2>
        <?php if ($cart_items->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    while ($item = $cart_items->fetch_assoc()):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>
                            <form method="post" action="update_cart.php" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="0" style="width: 60px;">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>KSh <?php echo number_format($item['price'], 2); ?></td>
                        <td>KSh <?php echo number_format($subtotal, 2); ?></td>
                        <td><a href="remove_from_cart.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-danger">Remove</a></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>KSh <?php echo number_format($total, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty. <a href="/products.php" class="btn btn-link">Continue Shopping</a></p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
