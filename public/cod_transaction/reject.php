<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];
$cod_id = (int)($_POST['cod_id'] ?? 0);

if ($cod_id <= 0) {
    die("COD tidak valid");
}

/* ambil COD */
$stmt = $pdo->prepare("SELECT * FROM cod_transactions WHERE id = ?");
$stmt->execute([$cod_id]);
$cod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cod) {
    die("COD tidak ditemukan");
}

/* validasi seller */
if ($user_id != $cod['seller_id']) {
    die("Akses ditolak");
}

/* update status */
$stmt = $pdo->prepare("
    UPDATE cod_transactions
    SET status = 'rejected'
    WHERE id = ?
");
$stmt->execute([$cod_id]);

/* cari room */
$stmt = $pdo->prepare("
    SELECT id
    FROM chat_rooms
    WHERE buyer_id = ? AND seller_id = ? AND product_id = ?
    LIMIT 1
");
$stmt->execute([
    $cod['buyer_id'],
    $cod['seller_id'],
    $cod['product_id']
]);
$room_id = $stmt->fetchColumn();

/* system message */
$stmt = $pdo->prepare("
    INSERT INTO chat_messages (room_id, sender_id, receiver_id, message)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([
    $room_id,
    $cod['seller_id'],
    $cod['buyer_id'],
    '[SYSTEM] Seller menolak pengajuan COD.'
]);

header("Location: ../chat/index.php?room=" . $room_id);
exit;
