<?php
require_once __DIR__ . "/../../includes/config.php";

$img_id = $_GET['id'];
$pid    = $_GET['pid'];

$stmt = $pdo->prepare("SELECT * FROM product_images WHERE id=?");
$stmt->execute([$img_id]);
$img = $stmt->fetch();

if ($img) {
    $file = __DIR__ . "/../../uploads/products/" . $img['url'];
    if (file_exists($file)) unlink($file);

    $pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$img_id]);
}

// jika primary dihapus, set ulang primary
$check = $pdo->prepare("SELECT * FROM product_images WHERE product_id=?");
$check->execute([$pid]);
$list = $check->fetchAll();

if (count($list) > 0) {
    $pdo->prepare("UPDATE product_images SET is_primary=1 WHERE id=?")
        ->execute([$list[0]['id']]);
}

header("Location: product_edit.php?id=$pid");
exit;
