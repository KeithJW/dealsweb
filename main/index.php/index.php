<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deals by Keith | Best Deals in Kenya</title>
    <meta name="description" content="Find the best deals on electronics, fashion, food, and more at Deals by Keith. Shop quality products at unbeatable prices.">
    <meta name="keywords" content="Deals by Keith, Kenya shopping, electronics, fashion, food, online shop">
    <meta property="og:title" content="Deals by Keith">
    <meta property="og:description" content="Shop the best deals on electronics, fashion, food & more in Kenya.">
    <meta property="og:image" content="/assets/images/banner.jpg">
    <link rel="icon" href="/assets/images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Deals by Keith</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link" href="/products/search.php">Search</a>
                <a class="nav-link" href="/cart/cart.php">Cart</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="/accounts/logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="/accounts/login.php">Login</a>
                    <a class="nav-link" href="/accounts/register.php">Register</a>
                <?php endif; ?>
                <a class="nav-link" href="/main/contact.php">Contact</a>
            </div>
        </div>
    </div>
</nav>

<!-- Carousel Banner -->
<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="/assets/images/banner1.jpg" class="d-block w-100" alt="Best Deals">
        </div>
        <div class="carousel-item">
            <img src="/assets/images/banner2.jpg" class="d-block w-100" alt="Shop Electronics">
        </div>
        <div class="carousel-item">
            <img src="/assets/images/banner3.jpg" class="d-block w-100" alt="Fashion & More">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="container mt-5">
    <h2 class="mb-4">Shop by Category</h2>
    <div class="row">
        <?php
        $categories = mysqli_query($conn, "SELECT * FROM categories");
        if (!$categories) {
            echo "<p class='text-danger'>Error loading categories: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
        } else {
            while ($category = mysqli_fetch_assoc($categories)):
        ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <a href="/products/category.php?id=<?php echo urlencode($category['id']); ?>" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
            </div>
        <?php endwhile; } ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="mb-4">Featured Products</h2>
    <div class="row">
        <!-- Example static featured products; replace with dynamic content later -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <img src="/assets/images/product1.jpg" class="card-img-top" alt="Product 1">
                <div class="card-body">
                    <h6 class="card-title">Wireless Headphones</h6>
                    <p class="card-text">Ksh 4,500</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <img src="/assets/images/product2.jpg" class="card-img-top" alt="Product 2">
                <div class="card-body">
                    <h6 class="card-title">Smartphone X</h6>
                    <p class="card-text">Ksh 25,000</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
        <!-- Add more featured product cards as needed -->
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Deals by Keith</h5>
                <p>Your trusted Kenyan online shop for the best deals on electronics, fashion, and more.</p>
            </div>
            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-white">Home</a></li>
                    <li><a href="/products/search.php" class="text-white">Search</a></li>
                    <li><a href="/main/contact.php" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact Us</h6>
                <p>Email: keithjuma672@gmail.com</p>
                <p>Phone: 0114 521 153</p>
                <p>Till Number: 8253176 (Princess Hadassah)</p>
            </div>
        </div>
        <div class="text-center mt-3">
            &copy; <?php echo date('Y'); ?> Deals by Keith. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
