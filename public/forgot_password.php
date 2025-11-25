<?php

require_once __DIR__ . '/../includes/config.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lupa Password</title>
</head>

<body>
    <h2>Lupa Password</h2>
    <p>Masukkan email kamu. Kami akan kirimkan OTP 6 digit untuk verifikasi.</p>
    <form method="post" action="send_otp.php">
        <label>Email</label><br><input type="email" name="email" required><br><br>
        <button type="submit">Kirim OTP</button>
    </form>
</body>

</html>