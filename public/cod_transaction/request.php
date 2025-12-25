<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];
$room_id = (int)($_POST['room_id'] ?? 0);

if ($room_id <= 0) die("Room tidak valid");

/* 1. Ambil chat room */
$stmt = $pdo->prepare("SELECT * FROM chat_rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) die("Chat room tidak ditemukan");
if ($user_id != $room['buyer_id']) die("Hanya buyer yang bisa mengajukan COD");

/* 2. Cek apakah sudah ada COD yang sedang aktif (Status selain yang selesai) */
$stmt = $pdo->prepare("
    SELECT id FROM cod_transactions 
    WHERE buyer_id = ? AND seller_id = ? AND product_id = ? 
    AND status NOT IN ('cancelled','rejected','completed')
    LIMIT 1
");
$stmt->execute([$room['buyer_id'], $room['seller_id'], $room['product_id']]);
if ($stmt->fetch()) {
    // Jika sudah ada, jangan insert lagi, langsung kembalikan ke chat
    header("Location: ../chat/index.php?room=" . $room_id);
    exit;
}

/* 3. Ambil nomor HP */
$stmt = $pdo->prepare("SELECT phone FROM accounts WHERE id = ?");
$stmt->execute([$room['buyer_id']]);
$buyer_phone = $stmt->fetchColumn() ?: '';

$stmt->execute([$room['seller_id']]);
$seller_phone = $stmt->fetchColumn() ?: '';

/* 4. Insert COD dengan status 'pending' yang JELAS */
try {
    $stmt = $pdo->prepare("
        INSERT INTO cod_transactions 
        (buyer_id, seller_id, product_id, qty, price, total, meeting_location, meeting_time, buyer_phone, seller_phone, status) 
        VALUES (?, ?, ?, 1, 0, 0, '', NOW(), ?, ?, 'pending')
    ");
    $stmt->execute([
        $room['buyer_id'],
        $room['seller_id'],
        $room['product_id'],
        $buyer_phone,
        $seller_phone
    ]);

    /* 5. Kirim system message */
    $stmt = $pdo->prepare("INSERT INTO chat_messages (room_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $room_id,
        $room['buyer_id'],
        $room['seller_id'],
        '[SYSTEM] Buyer mengajukan transaksi COD. Menunggu respon seller.'
    ]);
} catch (PDOException $e) {
    die("Gagal membuat transaksi: " . $e->getMessage());
}

header("Location: ../chat/index.php?room=" . $room_id);
exit;
