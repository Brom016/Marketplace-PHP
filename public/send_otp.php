<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit;
}

$email = trim($_POST['email']);
$user = find_user_by_email($email);
if (!$user) {
    header('Location: verify_otp.php?sent=1');
    exit;
}

$otp = generate_otp();
set_otp_for_user($user['id'], $otp, 15);

// prepare email
$subject = 'OTP Reset Password';
$body_html = "<p>Halo {$user['name']},</p>
<p>Gunakan kode OTP berikut untuk mereset password (berlaku 15 menit):</p>
<h2>{$otp}</h2>
<p>Jika kamu tidak meminta ini, abaikan email ini.</p>";

$sent = send_email($user['email'], $user['name'], $subject, $body_html);

if ($sent) {
    header('Location: verify_otp.php?sent=1&email=' . urlencode($email));
    exit;
} else {
    die('Gagal mengirim email OTP. Cek konfigurasi SMTP.');
}
