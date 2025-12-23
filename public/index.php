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

$profile_pic = "assets/img/default-profile.png";
if ($user && !empty($user['profile_picture'])) {
    $profile_pic = "../uploads/profile/" . $user['profile_picture'];
}

// Hitung total user
$total_users = $pdo->query("SELECT COUNT(*) FROM accounts WHERE role IN ('buyer','seller')")->fetchColumn();

// Hitung total produk
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Ambil kategori
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Cek kategori dari URL
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;

// =========================
// AMBIL DATA PRODUK + RATING
// =========================
$sql = "
    SELECT 
        p.*,
        a.name AS seller_name,
        a.username AS seller_username,
        (
            SELECT url 
            FROM product_images 
            WHERE product_id = p.id 
            ORDER BY is_primary DESC, id ASC 
            LIMIT 1
        ) AS thumb,
        (
            SELECT ROUND(AVG(rating),1)
            FROM product_reviews
            WHERE product_id = p.id
        ) AS avg_rating,
        (
            SELECT COUNT(*)
            FROM product_reviews
            WHERE product_id = p.id
        ) AS total_reviews
    FROM products p
    JOIN accounts a ON a.id = p.seller_id
";

if ($category_id) {
    $sql .= " WHERE p.category_id = :cat ";
}

$sql .= " ORDER BY p.created_at DESC LIMIT 18";

$stmt = $pdo->prepare($sql);

if ($category_id) {
    $stmt->bindValue(':cat', $category_id, PDO::PARAM_INT);
}

$stmt->execute();
$products = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>CampusMarket — Beranda</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
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
            <a href="index.php" class="cm-logo">
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
                <a href="cart/cart.php" class="cm-icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_total > 0): ?>
                        <span class="cm-badge green"><?= $cart_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- CHAT -->
                <a href="chat/index.php" class="cm-icon-btn">
                    <i class="fa-solid fa-message"></i>
                    <?php if ($chat_total > 0): ?>
                        <span class="cm-badge"><?= $chat_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- NOTIFICATIONS -->
                <a href="notifications/" class="cm-icon-btn">
                    <i class=""></i>
                    <?php if ($notif_total > 0): ?>
                        <span class=""><?= $notif_total ?></span>
                    <?php endif; ?>
                </a>

                <?php
                $current_page     = basename($_SERVER['PHP_SELF']);
                $is_profile_page  = ($current_page === 'profile/profile.php');
                ?>

                <?php if (!$user): ?>
                    <div class="cm-auth">
                        <a href="login.php">Sign In</a> |
                        <a href="register.php">Sign Up</a>
                    </div>

                <?php else: ?>
                    <div
                        class="cm-profile"
                        <?php if (!$is_profile_page): ?> onclick="window.location='profile/profile.php'" <?php endif; ?>>
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

    <div class="layout-container">

        <!-- SIDEBAR -->
        <div class="sidebar-box">
            <h3>Kategori</h3>

            <a href="index.php"
                class="cat-item <?= $category_id ? '' : 'active' ?>">
                <i class="bi bi-grid"></i> Semua Kategori
            </a>

            <?php foreach ($categories as $c): ?>
                <a href="index.php?category=<?= $c['id'] ?>"
                    class="cat-item <?= ($category_id == $c['id']) ? 'active' : '' ?>">
                    <i class="bi bi-tag"></i> <?= htmlspecialchars($c['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-box">

            <div class="banner-box">
                <h1>Selamat Datang di CampusMarket 🎓</h1>
                <p>Temukan produk dan jasa terbaik dari mahasiswa berbakat se-Semarang.</p>

                <div class="stats">
                    <div class="stat-item"><?= number_format($total_users) ?>+ Mahasiswa</div>
                    <div class="stat-item"><?= number_format($total_products) ?>+ Produk</div>
                </div>
            </div>

            <h2 class="title">Semua Produk</h2>
            <p class="subtitle">Menampilkan produk terbaru</p>

            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <a href="product/detail.php?id=<?= $p['id'] ?>" class="product-card">

                        <img src="<?= $p['thumb']
                                        ? '../uploads/products/' . $p['thumb']
                                        : '../assets/img/default-product.jpg' ?>">

                        <div class="product-info">
                            <div class="product-title">
                                <?= htmlspecialchars($p['name']) ?>
                            </div>

                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <?= $p['avg_rating'] ?: '0.0' ?>
                                (<?= $p['total_reviews'] ?> ulasan)
                            </div>

                            <div class="product-price">
                                Rp <?= number_format($p['price'], 0, ',', '.') ?>
                            </div>
                        </div>

                    </a>
                <?php endforeach; ?>
            </div>


        </div>

    </div>
    <br>
    <br>
</body>
<?php include 'components/footer.php'; ?>

</html>