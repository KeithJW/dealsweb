<?php
require_once '../main/config.php';
session_start();

// CSRF token setup
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$product_id = $_GET['id'] ?? 0;

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

// Fetch related products (example: same category, excluding current)
$related_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->bind_param("si", $product['category'], $product['id']);
$related_stmt->execute();
$related_products = $related_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?> - Deals by Keith</title>
    <meta name="description" content="<?= htmlspecialchars(substr($product['description'], 0, 150)); ?>">
    <meta property="og:title" content="<?= htmlspecialchars($product['name']); ?>">
    <meta property="og:image" content="/media/<?= htmlspecialchars($product['image']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <!-- Same nav as index.php -->
</nav>

<div class="container mt-4">
    <h2><?= htmlspecialchars($product['name']); ?></h2>
    <div class="row">
        <div class="col-md-6">
            <img src="/media/<?= htmlspecialchars($product['image']); ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h3>KSh <?= number_format($product['price'], 2); ?></h3>
            <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
            <p>
                Stock: <?= (int)$product['stock']; ?>
                <?php if ($product['stock'] < 5): ?>
                    <span class="badge bg-warning text-dark">Low Stock</span>
                <?php endif; ?>
            </p>

            <form action="/cart/add_to_cart.php" method="POST" class="d-flex align-items-center gap-2">
                <input type="hidden" name="id" value="<?= $product['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock']; ?>" class="form-control w-25" required>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>

    <hr class="my-5">

    <h4>Related Products</h4>
    <div class="row">
        <?php while ($related = $related_products->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="/media/<?= htmlspecialchars($related['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($related['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($related['name']); ?></h5>
                        <p class="card-text">KSh <?= number_format($related['price'], 2); ?></p>
                        <a href="/product.php?id=<?= $related['id']; ?>" class="btn btn-outline-primary w-100">View</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
