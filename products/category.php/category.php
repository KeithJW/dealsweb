<?php
require_once '../main/config.php';

// Validate and sanitize the 'id' from GET
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category securely
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo "<p>Category not found.</p>";
    exit;
}

// Fetch products securely
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$products = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - Deals by Keith</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Include your navbar here -->
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4"><?php echo htmlspecialchars($category['name']); ?></h2>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="/media/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($product['name']); ?>
                                <?php if ($product['is_new']): ?>
                                    <span class="badge bg-success">New</span>
                                <?php endif; ?>
                                <?php if ($product['on_sale']): ?>
                                    <span class="badge bg-danger">Sale</span>
                                <?php endif; ?>
                            </h5>
                            <p class="card-text">
                                KSh <?php echo number_format($product['price'], 2); ?><br>
                                <small><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</small>
                            </p>
                            <?php if ($product['in_stock']): ?>
                                <span class="badge bg-primary mb-2">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-secondary mb-2">Out of Stock</span>
                            <?php endif; ?>
                            <div class="d-grid gap-2">
                                <a href="product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                <?php if ($product['in_stock']): ?>
                                    <a href="/cart/add_to_cart.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-secondary btn-sm">Add to Cart</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
