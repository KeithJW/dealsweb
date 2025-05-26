<?php
session_start();
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Update with your HOSTAFRICA MySQL user
define('DB_PASS', 'your_password'); // Update with your MySQL password
define('DB_NAME', 'dealsbykeith');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Site settings (default colors)
$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));
if (!$settings) {
    $settings = ['primary_color' => '#004080', 'secondary_color' => '#f0f8ff'];
}
?>