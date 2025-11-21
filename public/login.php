<?php

require_once __DIR__ . '/../includes/config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$registered = isset($_GET['registered']);
$err = isset($_GET['err']) ? $_GET['err'] : null;
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <?php if ($registered) echo "<p>Registrasi berhasil, silakan login.</p>"; ?>
    <?php if ($err) echo "<p style='color:red;'>$err</p>"; ?>
    <form method="post" action="login_process.php">
        <label>Username or Email</label><br><input type="text" name="identity" required><br>
        <label>Password</label><br><input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p><a href="forgot_password.php">Lupa password?</a></p>
    <p><a href="register.php">Register</a></p>
</body>

</html>