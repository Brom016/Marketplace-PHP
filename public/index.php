<?php
require_once "../includes/config.php";
require_once "../includes/functions.php";



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

// Hitung jumlah mahasiswa (role buyer + seller)
$stmt = $pdo->query("SELECT COUNT(*) FROM accounts WHERE role IN ('buyer','seller')");
$total_users = $stmt->fetchColumn();

// Hitung jumlah produk
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

// Ambil kategori
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Ambil produk terbaru
$stmt = $pdo->query("
    SELECT p.*, a.name AS seller_name, a.username AS seller_username
    FROM products p
    JOIN accounts a ON a.id = p.seller_id
    ORDER BY p.created_at DESC
    LIMIT 9
");
$products = $stmt->fetchAll();

?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>CampusMarket — Beranda</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <a href="#" class="logo">
            CampusMarket
        </a>
        
        <div class="search">
            <form>
                <input type="text" placeholder="Cari produk mahasiswa...">
            </form>
        </div>

        <div class="menu-right">

            <!-- CART button -->
            <a href="cart/cart.php" class="cart-btn">🛒 Keranjang</a>

            <!-- CHAT BUTTON -->
            <a href="chat/chat.php" class="chat-btn">💬 Pesan</a>

            <?php if (!$user): ?>
                <!-- USER BLM LOGIN -->
                <div class="auth-buttons">
                    <a href="login.php" class="btn-auth">Sign In |</a>
                    <a href="register.php" class="btn-auth">Sign Up</a>
                </div>

            <?php else: ?>
                <!-- USER SDH LOGIN -->
                <div class="profile" onclick="window.location='profile/profile.php'" style="cursor:pointer;">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" class="profile-img">
                    <span><?= htmlspecialchars($user['name']) ?></span>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="layout">

        <!-- SIDEBAR KATEGORI -->
        <div class="sidebar">

            <h3>Kategori</h3>

            <ul>
                <li><a href="#">Semua Kategori</a></li>

                <?php foreach ($categories as $c): ?>
                    <li>
                        <a href="#">
                            <?= htmlspecialchars($c['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- <div class="promo-box"> 
                <h4>Promo Spesial!</h4>
                <p>Diskon hingga 50% untuk produk pilihan mahasiswa</p>
                <button>Lihat Promo</button>
            </div>
            -->

        </div>

        <!-- HERO SECTION -->
        <div class="content">

            <div class="banner">
                <h2>Selamat Datang di CampusMarket!</h2>
                <p>Temukan produk dan jasa terbaik dari mahasiswa berbakat se-Semarang.</p>
                <div>
                    <span><?= number_format($total_users) ?>+ Mahasiswa Aktif</span> |
                    <span><?= number_format($total_products) ?>+ Produk Tersedia</span>
                </div>
            </div>

            <h3>Semua Produk</h3>

            <div class="product-list">

                <?php foreach ($products as $p): ?>
                    <div class="product-card">

                        <div class="product-image">
                            <img src="../assets/img/default-product.jpg" width="100%">
                        </div>

                        <h4><?= htmlspecialchars($p['name']) ?></h4>

                        <div class="rating">
                            ⭐ <?= rand(40, 50) / 10 ?> (<?= rand(10, 100) ?> ulasan)
                        </div>

                        <div class="price">
                            Rp <?= number_format($p['price'], 0, ',', '.') ?>
                        </div>

                        <div class="seller">
                            <?= htmlspecialchars($p['seller_name']) ?>
                            — <?= htmlspecialchars($p['seller_username']) ?>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>



</body>

</html>