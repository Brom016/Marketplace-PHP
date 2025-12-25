<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];

$cod_id   = (int)($_POST['cod_id'] ?? 0);
$price    = (int)($_POST['price'] ?? 0);
$location = trim($_POST['meeting_location'] ?? '');
$time     = $_POST['meeting_time'] ?? '';

if ($cod_id <= 0 || $price <= 0 || !$location || !$time) {
    die("Data tidak lengkap");
}

try {
    // 1. Ambil data COD untuk validasi
    $stmt = $pdo->prepare("SELECT * FROM cod_transactions WHERE id = ?");
    $stmt->execute([$cod_id]);
    $cod = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cod) die("COD tidak ditemukan");
    if ($user_id != $cod['seller_id']) die("Akses ditolak");
    if ($cod['status'] !== 'pending') die("Transaksi sudah diproses");

    // 2. Update status ke 'waiting_buyer'
    $total = $price * $cod['qty'];
    $update = $pdo->prepare("
    UPDATE cod_transactions 
    SET price = ?, 
        total = ?, 
        meeting_location = ?, 
        meeting_time = ?, 
        status = 'waiting_buyer' 
    WHERE id = ?
");
    $update->execute([$price, $total, $location, $time, $cod_id]);

    // 3. Ambil Room ID untuk pesan chat
    $roomStmt = $pdo->prepare("
        SELECT id FROM chat_rooms 
        WHERE buyer_id = ? AND seller_id = ? AND product_id = ? 
        LIMIT 1
    ");
    $roomStmt->execute([$cod['buyer_id'], $cod['seller_id'], $cod['product_id']]);
    $room_id = $roomStmt->fetchColumn();

    // 4. Kirim pesan sistem jika room ketemu
    if ($room_id) {
        $msg = $pdo->prepare("
            INSERT INTO chat_messages (room_id, sender_id, receiver_id, message) 
            VALUES (?, ?, ?, ?)
        ");
        $msg->execute([
            $room_id,
            $user_id,
            $cod['buyer_id'],
            '[SYSTEM] Seller menyetujui COD. Silakan konfirmasi jika barang sudah diterima.'
        ]);
    }

    // 5. Redirect balik ke chat
    header("Location: ../chat/index.php?room=" . $room_id);
    exit;
} catch (PDOException $e) {
    // Jika ada error database, tampilkan di sini
    die("Database Error: " . $e->getMessage());
}
