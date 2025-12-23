<?php
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_POST['cart_id'])) {
    header("Location: cart.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM wishes WHERE id = ?");
$stmt->execute([$_POST['cart_id']]);

header("Location: cart.php");
exit;
