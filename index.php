<?php
$target = __DIR__ . '/public/index.php';
if (!file_exists($target)) {
    die('public/index.php tidak ditemukan.');
}
header("Location: public/index.php");
exit;
