<?php
require_once "../../includes/config.php";

if (!isset($_SESSION['user_id'], $_POST['product_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id    = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];


$stmt = $pdo->prepare("
    SELECT seller_id, stock
    FROM products
    WHERE id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ../index.php");
    exit;
}


if ($product['seller_id'] == $user_id) {
    header("Location: detail.php?id=" . $product_id);
    exit;
}


if ($product['stock'] <= 0) {
    header("Location: detail.php?id=" . $product_id);
    exit;
}


$stmt = $pdo->prepare("
    INSERT INTO wishes (user_id, product_id, quantity)
    VALUES (?, ?, 1)
    ON DUPLICATE KEY UPDATE quantity = quantity + 1
");
$stmt->execute([$user_id, $product_id]);

header("Location: ../cart/cart.php");
exit;
