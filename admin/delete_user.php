<?php
require_once __DIR__ . '/../includes/config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
$stmt->execute([$id]);

header("Location: users.php");
