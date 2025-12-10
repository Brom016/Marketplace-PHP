<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid product ID.");
}


$imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

foreach ($images as $img) {
    $filePath = __DIR__ . "/../uploads/products/" . $img['image_url'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);

$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

header("Location: products.php");
exit;
