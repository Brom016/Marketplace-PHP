<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

function find_user_by_email($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function find_user_by_username($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_user($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO accounts (name, username, email, phone, password, role, city, address, profile_picture) VALUES (:name, :username, :email, :phone, :password, :role, :city, :address, :profile_picture)");
    return $stmt->execute($data);
}

function generate_otp() {
    return strval(random_int(100000, 999999));
}

function set_otp_for_user($user_id, $otp, $minutes = 15) {
    global $pdo;
    $expires = date('Y-m-d H:i:s', time() + ($minutes * 60));
    $stmt = $pdo->prepare("UPDATE accounts SET otp_code = :otp, otp_expired = :exp WHERE id = :id");
    return $stmt->execute(['otp' => $otp, 'exp' => $expires, 'id' => $user_id]);
}

function clear_otp_for_user($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE accounts SET otp_code = NULL, otp_expired = NULL WHERE id = :id");
    return $stmt->execute(['id' => $user_id]);
}
