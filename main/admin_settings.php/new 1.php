<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: /accounts/login.php");
    exit;
}

// CSRF token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $primary_color = $_POST['primary_color'];
    $secondary_color = $_POST['secondary_color'];

    // Validate color format (# followed by 6 hex digits)
    if (!preg_match('/^#[a-f0-9]{6}$/i', $primary_color) || !preg_match('/^#[a-f0-9]{6}$/i', $secondary_color)) {
        die("Invalid color format.");
    }

    $stmt = $conn->prepare("UPDATE settings SET primary_color = ?, secondary_color = ? WHERE id = 1");
    $stmt->bind_param("ss", $primary_color, $secondary_color);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_settings.php?success=1");
    exit;
}

$result = $conn->query("SELECT * FROM settings WHERE id = 1");
$settings = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Deals by Keith</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <!-- Include your site nav here -->
    </nav>
    <div class="container mt-4">
        <h2>Customize Site</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Settings updated successfully!</div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label for="primary_color" class="form-label">Primary Color</label>
                <input type="color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="secondary_color" class="form-label">Secondary Color</label>
                <input type="color" id="secondary_color" name="secondary_color" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
