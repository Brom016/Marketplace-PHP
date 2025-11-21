<?php

require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: forgot_password.php');
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
</head>

<body>
    <h2>Reset Password</h2>
    <form method="post" action="reset_password_process.php">
        <label>New Password</label><br><input type="password" name="password" required><br>
        <label>Confirm Password</label><br><input type="password" name="password_confirm" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>