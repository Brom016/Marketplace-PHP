<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Home</title></head>
<body>
<h2>Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)</h2>
<p>Ini halaman utama untuk buyer/seller.</p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
