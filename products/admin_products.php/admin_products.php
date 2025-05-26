<?php
require_once '../main/config.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: /accounts/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock']);

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowed_types)) {
        die("Invalid file type. Only JPG, PNG, GIF allowed.");
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid('product_', true) . '.' . $ext;
    $upload_path = '../media/' . $new_filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        die("Failed to upload image.");
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $category_id, $stock, $new_filename);
    $stmt->execute();

    header("Location: admin_products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Deals by Keith</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <!-- Same nav as index.php -->
</nav>
<div class="container mt-4">
    <h2>Manage Products</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php
                $categories = mysqli_query($conn, "SELECT * FROM categories");
                while ($category = mysqli_fetch_assoc($categories)):
                ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>

    <h3 class="mt-5">Products</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $products = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id");
        while ($product = mysqli_fetch_assoc($products)):
        ?>
            <tr>
                <td><img src="../media/<?php echo $product['image']; ?>" width="50"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td>KSh <?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['stock']; ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
