<?php
// root/public/profile/profile.php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. AMBIL DATA USER
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan.");
}

// 2. HITUNG PRODUK (Hanya jika user adalah seller)
$product_count = 0;
if ($user['role'] == 'seller') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);
    $product_count = $stmt->fetchColumn();
}

// 3. HITUNG TRANSAKSI COD BERJALAN (Untuk Badge Tab)
// Kita hitung yang statusnya Pending, Waiting, atau Approved (Butuh perhatian)
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM cod_transactions 
    WHERE (buyer_id = ? OR seller_id = ?) 
    AND status IN ('pending', 'waiting_buyer', 'approved')
");
$stmt->execute([$user_id, $user_id]);
$cod_active_count = $stmt->fetchColumn();

// 4. HITUNG RATING & ULASAN
$review_count = 0;
$avg_rating = 0;
if ($user['role'] == 'seller') {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as review_count, COALESCE(AVG(rating), 0) as avg_rating
        FROM product_reviews pr
        INNER JOIN products p ON pr.product_id = p.id
        WHERE p.seller_id = ?
    ");
    $stmt->execute([$user_id]);
    $review_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $review_count = $review_data['review_count'];
    $avg_rating = round($review_data['avg_rating'], 1);
}

// 5. HITUNG PESAN BELUM DIBACA
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM chat_messages 
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$unread_msg_count = $stmt->fetchColumn();


// Setup Tampilan
$profile_pic = "../../uploads/profile/" . ($user['profile_picture'] ?: "default-profile.png");
// Fallback jika file tidak ada, pakai default system
if (!file_exists($profile_pic) && $user['profile_picture']) {
    $profile_pic = "../../assets/images/default-profile.png"; // Sesuaikan path default
}
$join_date = date("F Y", strtotime($user['created_at']));

// Helper Format Tanggal Indo
function formatTgl($date) {
    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)-1] . ' ' . date('Y - H:i', $ts);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — CampusMarket</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* CSS Tambahan untuk Badge & Rapih-rapih */
        .badge-count {
            background-color: #ff3b30;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
            min-width: 18px;
            text-align: center;
            display: inline-block;
            vertical-align: middle;
        }
        
        .transaction-card {
            display: flex;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            gap: 15px;
            align-items: center;
            transition: transform 0.2s;
        }
        .transaction-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .transaction-image img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
        }
        .transaction-info { flex: 1; }
        .transaction-info h4 { margin: 0 0 5px 0; font-size: 1rem; color: #333; }
        .transaction-price { font-weight: bold; color: #2196f3; margin-bottom: 5px; }
        .transaction-meta { font-size: 0.85rem; color: #666; }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        /* Warna Status */
        .status-pending { background: #fff3cd; color: #856404; }
        .status-waiting_buyer { background: #cce5ff; color: #004085; }
        .status-approved { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled, .status-rejected { background: #f8d7da; color: #721c24; }
        
        .empty-state { text-align: center; padding: 30px; background: #f9f9f9; border-radius: 8px; color: #888; }
    </style>
</head>

<body>
    <!-- HEADER SECTION -->
    <?php
    // Logic Header Ringkas
    $cart_total = 0;
    $notif_total = 0;
    
    // Hitung Wishlist/Cart
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_total = (int)$stmt->fetchColumn();

    // Hitung Notifikasi
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $notif_total = (int)$stmt->fetchColumn();
    
    // Logo Path
    $logo = "../../assets/images/icontrs.png"; // Pastikan path ini benar
    ?>

    <div class="cm-header">
        <div class="cm-container">
            <!-- LOGO -->
            <a href="../index.php" class="cm-logo">
                <img src="<?= $logo ?>" alt="Logo" onerror="this.src='../../assets/images/logo-placeholder.png'">
                <span>CampusMarket</span>
            </a>

            <!-- SEARCH -->
            <div class="cm-search">
                <form action="../search.php" method="GET">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Cari produk mahasiswa...">
                </form>
            </div>

            <!-- RIGHT MENU -->
            <div class="cm-menu-right">
                <!-- CART -->
                <a href="../cart/cart.php" class="cm-icon-btn" title="Keranjang">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_total > 0): ?>
                        <span class="cm-badge green"><?= $cart_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- CHAT -->
                <a href="../chat/index.php" class="cm-icon-btn" title="Chat">
                    <i class="fa-solid fa-message"></i>
                    <?php if ($unread_msg_count > 0): ?>
                        <span class="cm-badge"><?= $unread_msg_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- NOTIFICATIONS -->
                <a href="../notifications/" class="cm-icon-btn" title="Notifikasi">
                    <i class=""></i>
                    <?php if ($notif_total > 0): ?>
                        <span class="cm-badge"><?= $notif_total ?></span>
                    <?php endif; ?>
                </a>

                <!-- PROFILE -->
                <div class="cm-profile" onclick="window.location='profile.php'">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" class="cm-profile-img">
                    <div class="cm-user-info">
                        <span><?= htmlspecialchars(substr($user['name'], 0, 15)) ?></span>
                        <small><?= ucfirst($user['role']) ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HEADER END -->

    <!-- PROFILE CONTENT -->
    <div class="seller-profile">
        <div class="profile-card">
            <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-avatar">

            <div class="profile-info">
                <h2>
                    <?= htmlspecialchars($user['name']) ?>
                    <?php if($user['role'] == 'seller'): ?>
                        <span class="verified" style="color: #28a745; font-size: 0.6em; vertical-align: middle;">
                            <i class="fas fa-check-circle"></i> Seller
                        </span>
                    <?php endif; ?>
                </h2>

                <div class="meta">
                    <i class="far fa-calendar-alt"></i> Bergabung sejak <?= $join_date ?>
                </div>

                <div class="stats">
                    <div><strong><?= $product_count ?></strong> Produk</div>
                    <div>⭐ <strong><?= $avg_rating ?></strong> Rating</div>
                    <div><strong><?= $review_count ?></strong> Ulasan</div>
                </div>

                <?php if (!empty($user['description'])): ?>
                    <div class="description">
                        <?= nl2br(htmlspecialchars($user['description'])) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-btn">
                <div class="btn-row">
                    <a href="edit_profile.php" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profile</a>
                    <a href="../chat/index.php" class="btn btn-outline"><i class="fas fa-envelope"></i> Pesan</a>
                </div>

                <div class="btn-column">
                    <?php if ($user['role'] === 'seller'): ?>
                        <a href="../seller/dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard Seller
                        </a>
                    <?php else: ?>
                        <a href="../seller/register_seller.php" class="btn btn-secondary">
                            <i class="fas fa-store"></i> Daftar jadi Seller
                        </a>
                    <?php endif; ?>
                    <a href="../logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin logout?')">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MENU TABS -->
    <div class="product-section">
        <div class="menu-tabs">
            <a href="#" class="tab-item active">
                📦 Produk Saya 
                <?php if($product_count > 0): ?><span class="badge-count" style="background:#555"><?= $product_count ?></span><?php endif; ?>
            </a>
            
            <a href="../cod_transaction/index.php" class="tab-item">
                🤝 Transaksi COD 
                <?php if ($cod_active_count > 0): ?>
                    <span class="badge-count"><?= $cod_active_count ?></span>
                <?php endif; ?>
            </a>
            
            <a href="../chat/index.php" class="tab-item">
                💬 Chat 
                <?php if ($unread_msg_count > 0): ?>
                    <span class="badge-count"><?= $unread_msg_count ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- RECENT TRANSACTIONS SECTION -->
    <div class="product-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <div>
                <h3>Riwayat COD Terakhir</h3>
                <p class="section-subtitle">5 Transaksi terakhir Anda (Sebagai Pembeli & Penjual)</p>
            </div>
            <a href="../cod_transaction/history.php" style="text-decoration:none; color: #2196f3; font-weight:600;">Lihat Semua &rarr;</a>
        </div>

        <?php
        // LOGIC QUERY TRANSAKSI YANG DIPERBAIKI
        // Mengambil transaksi dimana user terlibat (baik sebagai buyer MAUPUN seller)
        $stmt = $pdo->prepare("
            SELECT 
                ct.*,
                p.name AS product_name,
                
                -- Ambil gambar produk utama
                (SELECT url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as product_image,
                
                -- Info Lawan Transaksi
                b.name AS buyer_name,
                s.name AS seller_name
                
            FROM cod_transactions ct
            JOIN products p ON ct.product_id = p.id
            JOIN accounts b ON ct.buyer_id = b.id
            JOIN accounts s ON ct.seller_id = s.id
            WHERE ct.buyer_id = ? OR ct.seller_id = ?
            ORDER BY ct.created_at DESC
            LIMIT 5
        ");
        
        $stmt->execute([$user_id, $user_id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (count($transactions) > 0): ?>
            <div class="transaction-list">
                <?php foreach ($transactions as $trans): ?>
                    <?php 
                        // Tentukan peran kita
                        $is_buyer = ($trans['buyer_id'] == $user_id);
                        $role_label = $is_buyer ? 'Pembeli' : 'Penjual';
                        $partner_name = $is_buyer ? $trans['seller_name'] : $trans['buyer_name'];
                        
                        // Gambar Produk
                        $prod_img = !empty($trans['product_image']) 
                            ? "../../uploads/products/" . $trans['product_image'] 
                            : "../../assets/images/no-image.png";
                    ?>
                    
                    <div class="transaction-card">
                        <div class="transaction-image">
                            <img src="<?= htmlspecialchars($prod_img) ?>" alt="Produk">
                        </div>

                        <div class="transaction-info">
                            <div style="display:flex; justify-content:space-between;">
                                <h4><?= htmlspecialchars($trans['product_name']) ?></h4>
                                <small style="color: #999;"><?= formatTgl($trans['created_at']) ?></small>
                            </div>
                            
                            <div class="transaction-price">
                                Rp <?= number_format($trans['total'], 0, ',', '.') ?>
                            </div>

                            <div class="transaction-meta">
                                <span style="margin-right: 10px; background: #f0f0f0; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">
                                    Anda: <strong><?= $role_label ?></strong>
                                </span>
                                <span>
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($partner_name) ?>
                                </span>
                            </div>

                            <?php if(!empty($trans['meeting_time'])): ?>
                                <div style="margin-top: 5px; font-size: 0.8rem; color: #555;">
                                    <i class="fas fa-clock"></i> Jadwal: <?= formatTgl($trans['meeting_time']) ?> WIB
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="transaction-status">
                            <?php
                                // Mapping status untuk class CSS
                                $s = $trans['status'];
                                $css_class = 'status-' . $s; 
                            ?>
                            <span class="status-badge <?= $css_class ?>">
                                <?= strtoupper($s) ?>
                            </span>
                            <br>
                            <a href="../cod_transaction/detail.php?id=<?= $trans['id'] ?>" style="display:inline-block; margin-top:5px; font-size:0.75rem; text-decoration:none; color:#2196f3;">Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <img src="../../assets/images/empty-box.png" alt="" style="width: 50px; opacity: 0.5; margin-bottom: 10px;">
                <p>Belum ada riwayat transaksi COD.</p>
                <a href="../index.php" class="btn btn-primary" style="font-size: 0.8rem;">Mulai Belanja</a>
            </div>
        <?php endif; ?>
    </div>

    <br><br><br>
    
    <?php include "../components/footer.php"; // Sesuaikan path footer jika perlu ?>
</body>
</html>