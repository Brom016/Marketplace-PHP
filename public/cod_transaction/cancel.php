<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];
$cod_id  = (int)($_POST['cod_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM cod_transactions
    WHERE id = ?
      AND buyer_id = ?
      AND status IN ('pending','approved')
");
$stmt->execute([$cod_id, $user_id]);
$cod = $stmt->fetch();

if (!$cod) {
    die("COD tidak valid");
}

$pdo->prepare("
    UPDATE cod_transactions
    SET status = 'cancelled'
    WHERE id = ?
    
")->execute([$cod_id]);

header("Location: ../chat/index.php?room=".$cod['chat_room_id']);
exit;
