<?php
require_once __DIR__ . '/../includes/config.php';
$sent = isset($_GET['sent']);
$email = isset($_GET['email']) ? $_GET['email'] : '';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Verifikasi OTP</title>
</head>

<body>
    <h2>Verifikasi OTP</h2>
    <?php if ($sent) echo "<p>OTP telah dikirim (cek inbox/spam).</p>"; ?>
    <form method="post" action="verify_otp_process.php">
        <label>Email</label><br><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
        <label>OTP (6 digit)</label><br><input type="text" name="otp" required pattern="\d{6}"><br><br>
        <button type="submit">Verifikasi</button>
    </form>
</body>

</html>