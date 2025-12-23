<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT id, username, name, email, phone, city, address, profile_picture, description
    FROM accounts
    WHERE id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan.");
}

/* ======================
   PATH GAMBAR (FIX)
====================== */
$profile_pic = !empty($user['profile_picture'])
    ? "../../uploads/profile/" . $user['profile_picture']
    : "../../uploads/profile/default-profile.png";
$errors = [];

/* ======================
   UPDATE PROFILE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = "Nama tidak boleh kosong.";
    }

    /* ======================
       UPLOAD FOTO
    ====================== */
    $photo_filename = $user['profile_picture'];

    if (!empty($_FILES['profile_picture']['name'])) {

        $file    = $_FILES['profile_picture'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format gambar harus JPG / PNG.";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Ukuran maksimum 2MB.";
        } else {

            $newname = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $uploadDir = __DIR__ . "/../../uploads/profile/";
            $dest = $uploadDir . $newname;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors[] = "Gagal mengunggah gambar.";
            } else {
                // hapus foto lama
                if (!empty($photo_filename)) {
                    $old = $uploadDir . $photo_filename;
                    if (file_exists($old)) {
                        @unlink($old);
                    }
                }
                $photo_filename = $newname;
            }
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare("
            UPDATE accounts
            SET name = ?, city = ?, address = ?, description = ?, profile_picture = ?
            WHERE id = ?
        ");
        $update->execute([
            $name,
            $city,
            $address,
            $description,
            $photo_filename,
            $user_id
        ]);

        header("Location: profile.php?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- HEADER JANGAN DIUBAH -->
    <?php

    $user_id = $user['id'] ?? null;

    // HITUNG WISHLIST
    $cart_total = 0;
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_total = (int)$stmt->fetchColumn();
    }

    // HITUNG NOTIFIKASI
    $notif_total = 0;
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        $notif_total = (int)$stmt->fetchColumn();
    }

    // HITUNG CHAT BELUM DIBACA
    $chat_total = 0;
    if ($user_id) {
        $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM chat_messages 
        WHERE receiver_id = ? 
        AND is_read = 0
    ");
        $stmt->execute([$user_id]);
        $chat_total = (int)$stmt->fetchColumn();
    }

    // SELECT PROFIL PIC fallback
    $iconA = "../assets/images/icontrs.png";
    $iconB = "../../assets/images/icontrs.png";
    $logo  = file_exists($iconA) ? $iconA : $iconB;
    ?>

    <!-- HEADER -->
    <div class="cm-header">
        <div class="cm-container">

            <!-- LOGO -->
            <a href="../index.php" class="cm-logo">
                <img src="<?= $logo ?>" alt="Logo">
                <span>CampusMarket</span>
            </a>

            <!-- SEARCH -->
            <div class="cm-search">
                <form>
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari produk mahasiswa...">
                </form>
            </div>

            <!-- RIGHT MENU -->
            <div class="cm-menu-right">

                <!-- CART (WISHLIST) -->
                <a href="../cart/cart.php" class="cm-icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_total > 0): ?>
                        <span class="cm-badge green"><?= $cart_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- CHAT -->
                <a href="../chat/chat.php" class="cm-icon-btn">
                    <i class="fa-solid fa-message"></i>
                    <?php if ($chat_total > 0): ?>
                        <span class="cm-badge"><?= $chat_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- NOTIFICATIONS -->
                <a href="../notifications/" class="cm-icon-btn">
                    <i class=""></i>
                    <?php if ($notif_total > 0): ?>
                        <span class=""><?= $notif_total ?></span>
                    <?php endif; ?>
                </a>

                <?php
                $current_page     = basename($_SERVER['PHP_SELF']);
                $is_profile_page  = ($current_page === 'profile.php');
                ?>

                <?php if (!$user): ?>
                    <div class="cm-auth">
                        <a href="login.php">Sign In</a> |
                        <a href="register.php">Sign Up</a>
                    </div>

                <?php else: ?>
                    <div
                        class="cm-profile"
                        <?php if (!$is_profile_page): ?> onclick="window.location='profile.php'" <?php endif; ?>>
                        <img src="<?= htmlspecialchars($profile_pic) ?>" class="cm-profile-img">
                        <div class="cm-user-info">
                            <span><?= htmlspecialchars($user['name']) ?></span>
                            <small>Mahasiswa</small>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- HEADER END -->

    <div class="edit-container">

        <h2>Edit Profil</h2>
        <p style="color:#888;margin-bottom:20px;">Perbarui informasi akun kamu</p>

        <?php if ($errors): ?>
            <div style="background:#ffd6d6;padding:12px;border-radius:8px;color:#900;margin-bottom:20px">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="edit-wrapper">

                <div class="edit-avatar">
                    <img
                        src="<?= htmlspecialchars($profile_pic) ?>"
                        id="avatarPreview"
                        class="profile-img-edit"
                        alt="Foto Profil">

                    <!-- INPUT ASLI (DISIMPAN) -->
                    <input
                        type="file"
                        id="profileUpload"
                        name="profile_picture"
                        accept="image/*"
                        hidden>

                    <!-- TOMBOL -->
                    <button type="button" class="btn-upload" onclick="triggerUpload()">
                        📷 Ganti Foto
                    </button>
                </div>

                <div class="edit-form">

                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">
                    </div>

                    <div class="input-group">
                        <label>Kota</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>">
                    </div>

                    <div class="input-group">
                        <label>Alamat</label>
                        <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>

                    <div class="input-group">
                        <label>Deskripsi</label>
                        <textarea name="description"><?= htmlspecialchars($user['description']) ?></textarea>
                    </div>

                    <button class="btn-primary" type="submit">
                        Simpan Perubahan
                    </button>

                </div>
            </div>
        </form>

    </div>

<script src="../../assets/js/upload.js"></script>
</body>
<?php include __DIR__ . '/../components/footer.php'; ?>
</html>