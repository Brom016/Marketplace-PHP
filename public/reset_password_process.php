<?php

require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reset_password.php');
    exit;
}

if (!isset($_SESSION['reset_user_id'])) {
    die('Session reset tidak ditemukan.');
}

$password = $_POST['password'];
$confirm = $_POST['password_confirm'];

if ($password !== $confirm) {
    die('Password konfirmasi tidak cocok.');
}

if (strlen($password) < 6) {
    die('Password minimal 6 karakter.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE accounts SET password = :pass, otp_code = NULL, otp_expired = NULL WHERE id = :id");
$ok = $stmt->execute(['pass' => $hash, 'id' => $_SESSION['reset_user_id']]);

if ($ok) {
    // clear session reset
    unset($_SESSION['reset_user_id']);
    header('Location: login.php?msg=reset_success');
    exit;
} else {
    die('Gagal reset password.');
}
