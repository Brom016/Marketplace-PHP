<?php
// root/public/profile/profile.php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get products count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE seller_id = ?");
$stmt->execute([$user_id]);
$product_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get COD transactions count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cod_transactions WHERE seller_id = ?");
$stmt->execute([$user_id]);
$cod_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get reviews count and average rating
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

// Get unread messages count
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT cm.room_id) as unread_count
    FROM chat_messages cm
    INNER JOIN chat_rooms cr ON cm.room_id = cr.id
    WHERE (cr.buyer_id = ? OR cr.seller_id = ?)
    AND cm.sender_id != ?
");
$stmt->execute([$user_id, $user_id, $user_id]);
$message_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

$profile_pic = "../../uploads/profile/" . ($user['profile_picture'] ?: "default-profile.png");
$join_date = date("F Y", strtotime($user['created_at']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — CampusMarket</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
</head>

<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<!-- PROFILE HEADER SECTION -->
<div class="seller-profile">
    <div class="profile-card">
        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-avatar">
        
        <div class="profile-info">
            <h2>
                <?= htmlspecialchars($user['name']) ?>
                <span class="verified">✓ Verified</span>
            </h2>
            
            <div class="meta">
                Bergabung sejak <?= $join_date ?> • Universitas Indonesia
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
            <a href="../chat/chat.php" class="btn-contact">Contact</a>
        </div>
    </div>
</div>

<!-- MENU TABS -->
<div class="product-section">
    <div class="menu-tabs">
        <a href="#" class="tab-item active">📦 My Products <?= $product_count ?></a>
        <a href="../cod_transaction/index.php" class="tab-item">🤝 COD Transaction <?= $cod_count ?></a>
        <a href="../chat/chat.php" class="tab-item">💬 New Message <?= $message_count ?></a>
    </div>
</div>

<!-- COD TRANSACTIONS SECTION -->
<div class="product-section">
    <h3>COD Transactions</h3>
    <p class="section-subtitle">Transaksi Cash on Delivery yang sedang berlangsung</p>
    
    <?php
    // Get COD transactions
    $stmt = $pdo->prepare("
        SELECT 
            ct.*,
            p.name as product_name,
            a.name as buyer_name,
            pi.url as product_image
        FROM cod_transactions ct
        INNER JOIN products p ON ct.product_id = p.id
        INNER JOIN accounts a ON ct.buyer_id = a.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE ct.seller_id = ?
        ORDER BY ct.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <?php if (count($transactions) > 0): ?>
    <div class="transaction-list">
        <?php foreach ($transactions as $trans): ?>
        <div class="transaction-card">
            <div class="transaction-image">
                <?php
                $prod_img = !empty($trans['product_image']) 
                    ? "../../uploads/products/" . $trans['product_image']
                    : "../../assets/images/default-product.png";
                ?>
                <img src="<?= htmlspecialchars($prod_img) ?>" alt="Product">
            </div>
            
            <div class="transaction-info">
                <h4><?= htmlspecialchars($trans['product_name']) ?></h4>
                <div class="transaction-price">Rp <?= number_format($trans['total'], 0, ',', '.') ?></div>
                
                <div class="transaction-meta">
                    <div class="buyer-info">
                        <img src="../../assets/images/icon.png" alt="Buyer" class="buyer-avatar">
                        <?= htmlspecialchars($trans['buyer_name']) ?>
                    </div>
                </div>
                
                <div class="meeting-info">
                    <strong>Titik Temu:</strong> <?= htmlspecialchars($trans['meeting_location']) ?><br>
                    <small><?= date('d M Y', strtotime($trans['meeting_time'])) ?></small>
                </div>
            </div>
            
            <div class="transaction-status">
                <?php
                $status_class = '';
                $status_text = '';
                
                switch($trans['status']) {
                    case 'pending':
                        $status_class = 'status-pending';
                        $status_text = 'Menunggu';
                        break;
                    case 'approved':
                        $status_class = 'status-approved';
                        $status_text = 'Selesai';
                        break;
                    case 'completed':
                        $status_class = 'status-completed';
                        $status_text = 'Selesai';
                        break;
                    case 'cancelled':
                        $status_class = 'status-cancelled';
                        $status_text = 'Dibatalkan';
                        break;
                }
                ?>
                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty">
        <p>Belum ada transaksi COD</p>
    </div>
    <?php endif; ?>
</div>
<a href="../seller/dashboard.php" class="back-dashboard-btn">Back to Dashboard</a>

<br>
<a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
<br>
<a href="../logout.php" class="logout-btn">Logout</a>
</body>
</html>