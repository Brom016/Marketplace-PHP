<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

// 1. Stress-test Role: Hanya BUYER yang boleh konfirmasi barang diterima!
$user_id = $_SESSION['user_id'];
$cod_id  = (int)($_POST['cod_id'] ?? 0);

if ($cod_id <= 0) {
    die("ID Transaksi tidak valid.");
}

/* ======================================================
   AMBIL DATA COD & VALIDASI SEBAGAI BUYER
====================================================== */
$stmt = $pdo->prepare("SELECT * FROM cod_transactions WHERE id = ?");
$stmt->execute([$cod_id]);
$cod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cod) die("Transaksi tidak ditemukan.");
if ($cod['buyer_id'] != $user_id) die("Akses ditolak! Hanya pembeli yang bisa konfirmasi.");
if ($cod['status'] !== 'waiting_buyer') die("Transaksi tidak dalam posisi menunggu konfirmasi.");

/* ======================================================
   CARI ROOM ID
====================================================== */
$stmt = $pdo->prepare("
    SELECT id FROM chat_rooms 
    WHERE buyer_id = ? AND seller_id = ? AND product_id = ? 
    LIMIT 1
");
$stmt->execute([$cod['buyer_id'], $cod['seller_id'], $cod['product_id']]);
$room_id = $stmt->fetchColumn();

/* ======================================================
   PROSES TRANSAKSI
====================================================== */
try {
    $pdo->beginTransaction();

    // A. Update status COD jadi 'completed'
    $stmt = $pdo->prepare("UPDATE cod_transactions SET status = 'completed' WHERE id = ?");
    $stmt->execute([$cod_id]);

    // B. Kurangi stok produk
    $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock > 0");
    $stmt->execute([$cod['product_id']]);

    // C. Kirim Pesan Sistem ke Chat
    if ($room_id) {
        $msg = $pdo->prepare("
            INSERT INTO chat_messages (room_id, sender_id, receiver_id, message) 
            VALUES (?, ?, ?, ?)
        ");
        // Pesan sistem dikirim atas nama sistem
        $msg->execute([
            $room_id,
            $user_id,
            $cod['seller_id'],
            '[SYSTEM] Buyer telah mengonfirmasi barang diterima. Transaksi Selesai ✅'
        ]);
    }

    $pdo->commit();

    // Redirect balik ke chat
    header("Location: ../chat/index.php?room=" . $room_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal menyelesaikan transaksi: " . $e->getMessage());
}
