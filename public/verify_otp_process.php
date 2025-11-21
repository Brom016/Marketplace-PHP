<?php

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: verify_otp.php');
    exit;
}

$email = trim($_POST['email']);
$otp = trim($_POST['otp']);

$user = find_user_by_email($email);
if (!$user) {
    die('User not found.');
}

if (empty($user['otp_code']) || empty($user['otp_expired'])) {
    die('Tidak ada OTP aktif. Silakan minta OTP lagi.');
}

if ($user['otp_code'] !== $otp) {
    die('OTP tidak cocok.');
}

if (strtotime($user['otp_expired']) < time()) {
    die('OTP kedaluwarsa. Silakan minta OTP baru.');
}


$_SESSION['reset_user_id'] = $user['id'];


header('Location: reset_password.php');
exit;
