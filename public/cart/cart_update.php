<?php
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_POST['cart_id'], $_POST['action'])) {
    header("Location: cart.php");
    exit;
}

$cart_id = (int)$_POST['cart_id'];
$action  = $_POST['action'];

// Ambil qty & stok
$stmt = $pdo->prepare("
    SELECT w.quantity, p.stock
    FROM wishes w
    JOIN products p ON p.id = w.product_id
    WHERE w.id = ?
");
$stmt->execute([$cart_id]);
$data = $stmt->fetch();

if (!$data) {
    header("Location: cart.php");
    exit;
}

$qty = $data['quantity'];
$stock = $data['stock'];

if ($action === 'plus' && $qty < $stock) {
    $qty++;
}
if ($action === 'minus' && $qty > 1) {
    $qty--;
}

$stmt = $pdo->prepare("UPDATE wishes SET quantity = ? WHERE id = ?");
$stmt->execute([$qty, $cart_id]);

header("Location: cart.php");
exit;
