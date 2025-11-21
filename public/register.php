<?php
require_once __DIR__ . '/../includes/config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Register</title>
</head>

<body>
    <h2>Register</h2>
    <form method="post" action="register_process.php">
        <label>Name</label><br><input type="text" name="name" required><br>
        <label>Username</label><br><input type="text" name="username" required><br>
        <label>Email</label><br><input type="email" name="email" required><br>
        <label>Phone</label><br><input type="text" name="phone" required><br>
        <label>Password</label><br><input type="password" name="password" required><br>
        <label>Role</label><br>
        <select name="role">
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select><br><br>
        <button type="submit">Register</button>
    </form>
    <p><a href="login.php">Login</a></p>
</body>

</html>