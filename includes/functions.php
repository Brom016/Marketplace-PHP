<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

function find_user_by_email($email)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function find_user_by_username($username)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_user($data)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO accounts (name, username, email, phone, password, role, city, address, profile_picture) VALUES (:name, :username, :email, :phone, :password, :role, :city, :address, :profile_picture)");
    return $stmt->execute($data);
}

function generate_otp()
{
    return strval(random_int(100000, 999999));
}

function set_otp_for_user($user_id, $otp, $minutes = 15)
{
    global $pdo;
    $expires = date('Y-m-d H:i:s', time() + ($minutes * 60));
    $stmt = $pdo->prepare("UPDATE accounts SET otp_code = :otp, otp_expired = :exp WHERE id = :id");
    return $stmt->execute(['otp' => $otp, 'exp' => $expires, 'id' => $user_id]);
}

function clear_otp_for_user($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE accounts SET otp_code = NULL, otp_expired = NULL WHERE id = :id");
    return $stmt->execute(['id' => $user_id]);
}


//sistem msg
/* =========================
   AUTH
========================= */
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /marketmm/public/login.php");
        exit;
    }
}

function isBuyer()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'buyer';
}

function isSeller()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'seller';
}

/* =========================
   SYSTEM MESSAGE
========================= */
function systemMessage(PDO $pdo, int $chat_room_id, string $message)
{
    $stmt = $pdo->prepare("
        INSERT INTO chat_messages (chat_room_id, sender_id, receiver_id, message, created_at)
        VALUES (?, NULL, NULL, ?, NOW())
    ");
    $stmt->execute([$chat_room_id, '[SYSTEM] ' . $message]);
}

/* =========================
   GET ACTIVE COD 
========================= */
function getActiveCOD(PDO $pdo, int $room_id)
{
    // ambil relasi dari chat room
    $stmt = $pdo->prepare("
        SELECT buyer_id, seller_id, product_id
        FROM chat_rooms
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch();

    if (!$room) {
        return null;
    }

    // cari COD aktif berdasarkan relasi logis
    $stmt = $pdo->prepare("
        SELECT *
        FROM cod_transactions
        WHERE buyer_id = ?
          AND seller_id = ?
          AND product_id = ?
          AND status NOT IN ('cancelled','rejected','completed')
        LIMIT 1
    ");
    $stmt->execute([
        $room['buyer_id'],
        $room['seller_id'],
        $room['product_id']
    ]);

    return $stmt->fetch();
}


//helper cod
function hasActiveCOD(PDO $pdo, int $buyer_id, int $seller_id, int $product_id): bool
{
    $stmt = $pdo->prepare("
        SELECT id
        FROM cod_transactions
        WHERE buyer_id = ?
          AND seller_id = ?
          AND product_id = ?
          AND status NOT IN ('cancelled','completed')
        LIMIT 1
    ");
    $stmt->execute([$buyer_id, $seller_id, $product_id]);
    return (bool) $stmt->fetchColumn();
}
