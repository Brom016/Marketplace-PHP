<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit("Access denied");
}

$image_id = $_GET['id'] ?? null;
$product_id = $_GET['pid'] ?? null;

if (!$image_id || !$product_id) {
    die("Invalid request");
}


$stmt = $pdo->prepare("SELECT * FROM product_images WHERE id=? AND product_id=?");
$stmt->execute([$image_id, $product_id]);
$img = $stmt->fetch();

if (!$img) die("Image not found!");


$filePath = __DIR__ . "/../uploads/products/" . $img['url'];
if (file_exists($filePath)) {
    unlink($filePath);
}


$pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$image_id]);

header("Location: edit_product.php?id=" . $product_id);
exit;
