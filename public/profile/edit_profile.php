<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, username, name, email, phone, city, address, profile_picture, description FROM accounts WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) { die("User tidak ditemukan."); }

$profile_pic = !empty($user['profile_picture']) ? "/uploads/profile/" . $user['profile_picture'] : "/uploads/profile/default-profile.png";
$errors = [];

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // validate minimal
    if ($name === '') { $errors[] = "Nama tidak boleh kosong."; }

    // handle file upload
    $photo_filename = $user['profile_picture'];
    if (!empty($_FILES['profile_picture']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $file = $_FILES['profile_picture'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format gambar harus JPG/PNG.";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Maksimum ukuran gambar 2MB.";
        } else {
            $newname = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $dest = __DIR__ . "/../../uploads/profile/" . $newname;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors[] = "Gagal mengunggah gambar.";
            } else {
                // remove old file if exists & not default
                if (!empty($photo_filename) && file_exists(__DIR__ . "/../../uploads/profile/" . $photo_filename)) {
                    @unlink(__DIR__ . "/../../uploads/profile/" . $photo_filename);
                }
                $photo_filename = $newname;
            }
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE accounts SET name = ?, city = ?, address = ?, description = ?, profile_picture = ? WHERE id = ?");
        $update->execute([$name, $city, $address, $description, $photo_filename, $user_id]);
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
</head>
<body>
<?php
if (file_exists(__DIR__ . "/../components/header.php")) {
    include __DIR__ . "/../components/header.php";
}
?>

<div class="edit-container">

    <h2>Edit Profil</h2>
    <hr style="margin:12px 0 20px 0;">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" style="background:#ffd6d6;padding:10px;border-radius:6px;margin-bottom:15px;color:#aa0000;">
            <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div style="display:flex; gap:20px; align-items:flex-start;">
            <div style="width:140px;">
                <img src="<?= htmlspecialchars($profile_pic) ?>" class="profile-img-edit" alt="Foto profil">
                <div style="margin-top:8px;">
                    <input type="file" name="profile_picture" accept="image/*">
                </div>
            </div>

            <div style="flex:1;">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">
                </div>

                <div class="input-group">
                    <label>Kota</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>">
                </div>

                <div class="input-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>
                </div>

                <div class="input-group">
                    <label>Deskripsi</label>
                    <textarea name="description"><?= htmlspecialchars($user['description']) ?></textarea>
                </div>

                <button class="btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>

</body>
</html>
