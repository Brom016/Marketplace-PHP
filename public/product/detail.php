<?php
//product/detail.php
require_once "../../includes/config.php";

/* =========================
   USER LOGIN (OPTIONAL)
========================= */
$user = null;
$user_id = null;
$profile_pic = "../../assets/img/default-profile.png";

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];
        if (!empty($user['profile_picture'])) {
            $profile_pic = "../../uploads/profile/" . $user['profile_picture'];
        }
    }
}

/* =========================
   VALIDASI PRODUK
========================= */
if (!isset($_GET['id'])) {
    die("Produk tidak ditemukan");
}

$product_id = (int)$_GET['id'];

/* =========================
   DATA PRODUK + SELLER
========================= */
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        a.id AS seller_id,
        a.name AS seller_name,
        a.username AS seller_username,
        a.profile_picture AS seller_photo,
        a.address AS seller_address
    FROM products p
    JOIN accounts a ON a.id = p.seller_id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Produk tidak ditemukan");
}

/* =========================
   GAMBAR PRODUK
========================= */
$stmt = $pdo->prepare("
    SELECT url 
    FROM product_images
    WHERE product_id = ?
    ORDER BY is_primary DESC, id ASC
    LIMIT 1
");
$stmt->execute([$product_id]);
$main_image = $stmt->fetchColumn();

/* =========================
   FOTO SELLER
========================= */
$seller_photo = !empty($product['seller_photo'])
    ? "../../uploads/profile/" . $product['seller_photo']
    : "../../assets/img/default-profile.png";

/* =========================
   PRODUK TERBARU (REKOMENDASI)
========================= */
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.name,
        p.price,
        (
            SELECT url 
            FROM product_images 
            WHERE product_id = p.id 
            ORDER BY is_primary DESC, id ASC 
            LIMIT 1
        ) AS image
    FROM products p
    WHERE p.id != ?
    ORDER BY p.created_at DESC
    LIMIT 6
");
$stmt->execute([$product_id]);
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>

    <!-- HEADER CSS -->
    <link rel="stylesheet" href="../../assets/css/header.css">

    <!-- PRODUCT CSS -->
    <link rel="stylesheet" href="../../assets/css/product.css">

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <a href="../chat/index.php" class="cm-icon-btn">
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
                        <?php if (!$is_profile_page): ?> onclick="window.location='../profile/profile.php'" <?php endif; ?>>
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

    <main class="main-content">
        <div class="container">

            <!-- PRODUCT -->
            <div class="product-section">

                <div class="product-image">
                    <img src="<?= $main_image
                                    ? "../../uploads/products/" . $main_image
                                    : "../../assets/img/default-product.jpg" ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>">
                </div>

                <div class="product-details">

                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                    <div class="product-price">
                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                    </div>

                    <!-- ALAMAT -->
                    <div class="product-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($product['seller_address']) ?>
                    </div>

                    <div class="meta-row">
                        <i class="fas fa-box"></i>
                        <span>Stok: <?= (int)$product['stock'] ?></span>
                    </div>

                    <div class="action-buttons">
                        <?php if (!$user): ?>
                            <a href="../login.php" class="btn btn-primary">
                                Login untuk membeli
                            </a>

                        <?php elseif ($user_id == $product['seller_id']): ?>
                            <button class="btn btn-outline" disabled>
                                Produk Anda
                            </button>

                        <?php else: ?>
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button class="btn btn-primary">
                                    <i class="fas fa-cart-plus"></i> Masukkan Keranjang
                                </button>
                            </form>

                            <form action="start_chat.php" method="POST">
                                <input type="hidden" name="seller_id" value="<?= $product['seller_id'] ?>">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button class="btn btn-outline">
                                    <i class="fas fa-message"></i> Chat Seller
                                </button>
                            </form>

                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- SELLER CARD -->
            <div class="seller-card">
                <img src="<?= htmlspecialchars($seller_photo) ?>" class="seller-avatar">
                <div>
                    <b><?= htmlspecialchars($product['seller_name']) ?></b><br>
                    <small>@<?= htmlspecialchars($product['seller_username']) ?></small>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="description-box">
                <h3>Deskripsi Produk</h3>
                <hr>
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            <?php if ($recommendations): ?>
                <div class="recommendation-box">
                    <h3>Produk Terbaru</h3>

                    <div class="recommendation-grid">
                        <?php foreach ($recommendations as $r): ?>
                            <a href="detail.php?id=<?= $r['id'] ?>" class="recommendation-card">
                                <div class="rec-image">
                                    <img src="<?= $r['image']
                                                    ? '../../uploads/products/' . $r['image']
                                                    : '../../assets/img/default-product.jpg' ?>">
                                </div>

                                <div class="rec-info">
                                    <div class="rec-name">
                                        <?= htmlspecialchars($r['name']) ?>
                                    </div>
                                    <div class="rec-price">
                                        Rp <?= number_format($r['price'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>

</body>

</html>