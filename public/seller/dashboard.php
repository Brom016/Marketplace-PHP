<?php
require_once __DIR__ . "/../../includes/config.php";

/* =========================
   AUTH
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'seller') {
    die("Akses ditolak");
}

$user_id = $user['id'];

/* =========================
   PROFILE PIC
========================= */
$profile_pic = "../../assets/img/default-profile.png";
if (!empty($user['profile_picture'])) {
    $candidate = "../../uploads/profile/" . $user['profile_picture'];
    if (file_exists($candidate)) {
        $profile_pic = $candidate;
    }
}

/* =========================
   HEADER COUNTS
========================= */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishes WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_total = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$notif_total = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM chat_messages 
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$chat_total = (int)$stmt->fetchColumn();

/* =========================
   DASHBOARD STATS
========================= */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ?");
$stmt->execute([$user_id]);
$total_products = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$active_products = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'draft'");
$stmt->execute([$user_id]);
$draft_products = (int)$stmt->fetchColumn();

/* =========================
   LATEST PRODUCTS + IMAGE
========================= */
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.name,
        p.price,
        pi.url AS image
    FROM products p
    LEFT JOIN product_images pi 
        ON pi.product_id = p.id 
        AND pi.is_primary = 1
    WHERE p.seller_id = ?
    ORDER BY p.created_at DESC
    LIMIT 6
");
$stmt->execute([$user_id]);
$latest_products = $stmt->fetchAll();



/* =========================
   LOGO
========================= */
$iconA = "../assets/images/icontrs.png";
$iconB = "../../assets/images/icontrs.png";
$logo  = file_exists($iconA) ? $iconA : $iconB;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Seller</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <!-- =========================
     HEADER
========================= -->
    <div class="cm-header">
        <div class="cm-container">

            <a href="../index.php" class="cm-logo">
                <img src="<?= $logo ?>">
                <span>CampusMarket</span>
            </a>

            <div class="cm-search">
                <form>
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari produk mahasiswa...">
                </form>
            </div>

            <div class="cm-menu-right">

                <a href="../cart/cart.php" class="cm-icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_total > 0): ?>
                        <span class="cm-badge green"><?= $cart_total ?></span>
                    <?php endif; ?>
                </a>

                <a href="../chat/index.php" class="cm-icon-btn">
                    <i class="fas fa-message"></i>
                    <?php if ($chat_total > 0): ?>
                        <span class="cm-badge"><?= $chat_total ?></span>
                    <?php endif; ?>
                </a>

                <a href="../notifications/" class="cm-icon-btn">
                    <i class=""></i>
                    <?php if ($notif_total > 0): ?>
                        <span class="cm-badge"><?= $notif_total ?></span>
                    <?php endif; ?>
                </a>

                <div class="cm-profile" onclick="location.href='../profile/profile.php'">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" class="cm-profile-img">
                    <div class="cm-user-info">
                        <span><?= htmlspecialchars($user['name']) ?></span>
                        <small>Seller</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- =========================
     DASHBOARD
========================= -->
    <div class="layout-container">

        <!-- SIDEBAR -->
        <div class="sidebar-box">
            <h3>Seller Panel</h3>

            <a href="dashboard.php" class="cat-item active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>

            <a href="products.php" class="cat-item">
                <i class="fas fa-box"></i> Kelola Produk
            </a>

            <a href="product_add.php" class="cat-item">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        </div>

        <!-- MAIN -->
        <div class="main-box">

            <!-- BANNER -->
            <div class="banner-box">
                <h1>Halo, <?= htmlspecialchars($user['name']) ?> 👋</h1>
                <p>Kelola produk dan pantau performa tokomu</p>

                <div class="stats">
                    <div class="stat-item">Total: <b><?= $total_products ?></b></div>
                    <div class="stat-item">Aktif: <b><?= $active_products ?></b></div>
                    <div class="stat-item">Draft: <b><?= $draft_products ?></b></div>
                </div>
            </div>

            

            <!-- PRODUK TERBARU -->
            <div class="title">Produk Terbaru</div>
            <div class="subtitle">Update terakhir dari tokomu</div>

            <div class="product-grid">
                <?php foreach ($latest_products as $p): ?>
                    <div class="product-card">

                        <?php
                        $img = !empty($p['image'])
                            ? "../../uploads/products/" . $p['image']
                            : "../../assets/img/default-product.png";
                        ?>

                        <img src="<?= htmlspecialchars($img) ?>"
                            alt="<?= htmlspecialchars($p['name']) ?>">

                        <div class="product-info">
                            <div class="product-title">
                                <?= htmlspecialchars($p['name']) ?>
                            </div>
                            <div class="product-price">
                                Rp<?= number_format($p['price'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


        </div>
    </div>

    <script>
        new Chart(document.getElementById('productChart'), {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Draft'],
                datasets: [{
                    data: [<?= $active_products ?>, <?= $draft_products ?>],
                    backgroundColor: ['#00b341', '#f8b400'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

</body>

</html>