<?php
require_once __DIR__ . "/../../includes/config.php";

$img_id = $_GET['id'];
$pid    = $_GET['pid'];

$pdo->prepare("UPDATE product_images SET is_primary=0 WHERE product_id=?")
    ->execute([$pid]);

$pdo->prepare("UPDATE product_images SET is_primary=1 WHERE id=?")
    ->execute([$img_id]);

header("Location: product_edit.php?id=$pid");
exit;
