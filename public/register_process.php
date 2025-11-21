<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$name = trim($_POST['name']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$role = in_array($_POST['role'], ['buyer', 'seller']) ? $_POST['role'] : 'buyer';

// basic validation
if (!$name || !$username || !$email || !$phone || !$password) {
    die('Semua field required.');
}

// unique checks
if (find_user_by_email($email)) {
    die('Email sudah terdaftar.');
}
if (find_user_by_username($username)) {
    die('Username sudah terdaftar.');
}

// hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

$created = create_user([
    'name' => $name,
    'username' => $username,
    'email' => $email,
    'phone' => $phone,
    'password' => $hash,
    'role' => $role,
    'city' => null,
    'address' => null,
    'profile_picture' => null
]);

if ($created) {
    header('Location: login.php?registered=1');
    exit;
} else {
    die('Gagal membuat user.');
}
