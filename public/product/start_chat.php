<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    die("Produk tidak valid");
}

$product_id = (int)$_POST['product_id'];
$user_id    = $_SESSION['user_id'];

/* ambil produk */
$stmt = $pdo->prepare("
    SELECT * FROM products
    WHERE id = ?
      AND status = 'active'
      AND stock > 0
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product || $product['seller_id'] == $user_id) {
    die("Produk tidak valid");
}

/* cek / buat chat */
$stmt = $pdo->prepare("
    SELECT id FROM chat_rooms
    WHERE buyer_id = ?
      AND seller_id = ?
      AND product_id = ?
    LIMIT 1
");
$stmt->execute([$user_id, $product['seller_id'], $product_id]);
$chat = $stmt->fetch();

if (!$chat) {
    $stmt = $pdo->prepare("
        INSERT INTO chat_rooms (buyer_id, seller_id, product_id, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $product['seller_id'], $product_id]);
    $chat_id = $pdo->lastInsertId();
} else {
    $chat_id = $chat['id'];
}

header("Location: /marketmm/public/chat/index.php?room=" . $chat_id);
exit;
