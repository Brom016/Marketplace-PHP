<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

requireLogin();

$user_id = $_SESSION['user_id'];

/* =========================
   VALIDASI INPUT
========================= */
if (!isset($_POST['room_id'], $_POST['message'])) {
    die("Data tidak lengkap");
}

$room_id = (int)$_POST['room_id'];
$message = trim($_POST['message']);

if ($room_id <= 0 || $message === '') {
    die("Data tidak valid");
}

/* =========================
   VALIDASI ROOM
========================= */
$stmt = $pdo->prepare("
    SELECT buyer_id, seller_id
    FROM chat_rooms
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    die("Room tidak ditemukan");
}

/* =========================
   VALIDASI AKSES
========================= */
if ($user_id != $room['buyer_id'] && $user_id != $room['seller_id']) {
    die("Akses ditolak");
}

/* =========================
   TENTUKAN RECEIVER
========================= */
$receiver_id = ($user_id == $room['buyer_id'])
    ? $room['seller_id']
    : $room['buyer_id'];

/* =========================
   INSERT CHAT MESSAGE
========================= */
$stmt = $pdo->prepare("
    INSERT INTO chat_messages
    (room_id, sender_id, receiver_id, message, is_read, created_at)
    VALUES (?, ?, ?, ?, 0, NOW())
");
$stmt->execute([
    $room_id,
    $user_id,
    $receiver_id,
    $message
]);

/* =========================
   UPDATE ROOM TIMESTAMP
========================= */
$stmt = $pdo->prepare("
    UPDATE chat_rooms
    SET updated_at = NOW()
    WHERE id = ?
");
$stmt->execute([$room_id]);

/* =========================
   REDIRECT BALIK KE CHAT
========================= */
header("Location: index.php?room=" . $room_id);
exit;
