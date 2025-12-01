<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$identity = trim($_POST['identity']);
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :ident OR username = :ident LIMIT 1");
$stmt->execute(['ident' => $identity]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php?err=User%20tidak%20ditemukan');
    exit;
}

if (!password_verify($password, $user['password'])) {
    header('Location: login.php?err=Password%20salah');
    exit;
}

// login success
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

// redirect based on role
if ($user['role'] === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
} elseif ($user['role'] === 'seller') {
    header('Location: seller/dashboard.php');
    exit;
} else {
    header('Location: index.php');
    exit;
}
