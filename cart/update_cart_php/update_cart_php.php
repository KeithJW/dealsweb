<?php
require_once '../main/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /accounts/login.php");
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Security check failed. Please try again.';
    } else {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        if ($item_id <= 0) {
            $error = 'Invalid item selected.';
        } elseif ($quantity < 0) {
            $error = 'Invalid quantity provided.';
        } else {
            if ($quantity > 0) {
                $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", $quantity, $item_id);
                    $stmt->execute();
                    $stmt->close();
                    $success = 'Cart updated successfully!';
                } else {
                    $error = 'A database error occurred. Please try again later.';
                }
            } else {
                $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $item_id);
                    $stmt->execute();
                    $stmt->close();
                    $success = 'Item removed from cart.';
                } else {
                    $error = 'A database error occurred. Please try again later.';
                }
            }
        }
    }
}

// Fetch current cart items (example)
$cart_items = [];
$result = $conn->query("SELECT id, product_name, quantity FROM cart_items WHERE user_id = {$_SESSION['user_id']}");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        .error {
            color: #fff;
            background-color: #e74c3c;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            color: #fff;
            background-color: #2ecc71;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<h2>Your Shopping Cart</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (count($cart_items) === 0): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Update</th>
        </tr>
        <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                    <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="0">
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
