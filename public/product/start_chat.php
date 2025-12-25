<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

requireLogin();

$user_id = $_SESSION['user_id'];

/* =========================
   VALIDASI INPUT (POST)
========================= */
if (!isset($_POST['product_id']) || !isset($_POST['seller_id'])) {
    die("Data tidak lengkap");
}

$product_id = (int)$_POST['product_id'];
$seller_id  = (int)$_POST['seller_id'];

if ($product_id <= 0 || $seller_id <= 0) {
    die("Data tidak valid");
}

/* =========================
   VALIDASI PRODUK
========================= */
$stmt = $pdo->prepare("
    SELECT id, seller_id, status, stock
    FROM products
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Produk tidak ditemukan");
}

if ($product['seller_id'] != $seller_id) {
    die("Seller tidak cocok");
}

if ($product['seller_id'] == $user_id) {
    die("Tidak bisa chat produk sendiri");
}

if ($product['status'] !== 'active' || $product['stock'] <= 0) {
    die("Produk tidak tersedia");
}

/* =========================
   CEK ROOM EXISTING
========================= */
$stmt = $pdo->prepare("
    SELECT id
    FROM chat_rooms
    WHERE buyer_id = ?
      AND seller_id = ?
      AND product_id = ?
    LIMIT 1
");
$stmt->execute([$user_id, $seller_id, $product_id]);
$room = $stmt->fetch();

/* =========================
   BUAT ROOM JIKA BELUM ADA
========================= */
if (!$room) {

    $stmt = $pdo->prepare("
        INSERT INTO chat_rooms
        (buyer_id, seller_id, product_id, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        $user_id,
        $seller_id,
        $product_id
    ]);

    $room_id = $pdo->lastInsertId();

    /* SYSTEM MESSAGE PERTAMA */
    $stmt = $pdo->prepare("
        INSERT INTO chat_messages
        (room_id, sender_id, receiver_id, message, is_read, created_at)
        VALUES (?, NULL, NULL, ?, 1, NOW())
    ");
    $stmt->execute([
        $room_id,
        '[SYSTEM] Chat dimulai'
    ]);
} else {
    $room_id = $room['id'];
}

/* =========================
   REDIRECT KE CHAT
========================= */
header("Location: /marketmm/public/chat/index.php?room=" . $room_id);
exit;
