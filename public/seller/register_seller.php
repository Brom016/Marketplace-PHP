<?php
require_once "../../includes/config.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil role user
$stmt = $pdo->prepare("SELECT role FROM accounts WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Sudah seller -> masuk dashboard
if ($user && $user['role'] === 'seller') {
    header("Location: dashboard.php");
    exit;
}

// Admin tidak boleh jadi seller
if ($user && $user['role'] === 'admin') {
    die("Admin tidak boleh menjadi seller.");
}

$error = "";

// Submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST['phone']);

    if (empty($phone)) {
        $error = "Nomor WhatsApp wajib diisi.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE accounts SET 
                role = 'seller',
                phone = ?
            WHERE id = ?
        ");

        if ($stmt->execute([$phone, $user_id])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Gagal menjadi seller.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Seller</title>
    <link rel="stylesheet" href="../../assets/css/profile.css">
</head>
<body>

<div class="edit-container">
    <h2>Daftar sebagai Seller</h2>
    <p class="section-subtitle">Masukkan nomor WhatsApp untuk mengaktifkan akun seller</p>

    <?php if (!empty($error)) : ?>
        <div style="color:red; margin-bottom:10px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="post" class="edit-form">

        <div class="input-group">
            <label>No. WhatsApp</label>
            <input 
                type="text" 
                name="phone" 
                placeholder="08xxxxxxxxxx" 
                required
            >
        </div>

        <button type="submit" class="btn-primary">
            Aktifkan Seller
        </button>

    </form>
</div>

</body>
</html>
