<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';




// Cek login
$user = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Default profile picture
$profile_pic = "assets/img/default-profile.png";
if ($user && !empty($user['profile_picture'])) {
    $profile_pic = "../uploads/profile/" . $user['profile_picture'];
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>CampusMarket — Profile</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <a href="../index.php" class="logo">CampusMarket</a>

        <div class="search">
            <form>
                <input type="text" placeholder="Cari produk mahasiswa...">
            </form>
        </div>

        <div class="menu-right">

            <!-- CART button -->
            <a href="../cart/cart.php" class="cart-btn">🛒 Keranjang</a>

            <!-- CHAT BUTTON -->
            <a href="../chat/chat.php" class="chat-btn">💬 Pesan</a>

            <?php if (!$user): ?>
                <!-- USER BLM LOGIN -->
                <div class="auth-buttons">
                    <a href="login.php" class="btn-auth">Sign In |</a>
                    <a href="register.php" class="btn-auth">Sign Up</a>
                </div>

            <?php else: ?>
                <!-- USER SDH LOGIN -->
                <div class="profile" onclick="window.location='#'" style="cursor:pointer;">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" class="profile-img">
                    <span><?= htmlspecialchars($user['name']) ?></span>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <center>
        <h1>Halaman Profile</h1>
        <h2></h1><a href="../logout.php" class="btn">Logout</a></h2>

    </center>
    

</body>

</html>