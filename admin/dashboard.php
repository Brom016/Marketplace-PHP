<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Dashboard</title></head>
<body>
<h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
<p><a href="../public/logout.php">Logout</a></p>
</body>
</html>
