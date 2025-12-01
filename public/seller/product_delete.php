<?php
require_once __DIR__ . "/../../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'seller') {
    die("Akses ditolak!");
}

$id = $_GET['id'];

// cek apakah produk milik seller
$check = $pdo->prepare("SELECT seller_id FROM products WHERE id = ?");
$check->execute([$id]);
$p = $check->fetch();

if (!$p || $p['seller_id'] != $user['id']) {
    die("Tidak boleh hapus produk orang lain!");
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: products.php");
exit;
