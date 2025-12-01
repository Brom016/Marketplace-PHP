<?php
// root/public/cod_transaction/index.php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'buyer';

// Filter status
$filter_status = $_GET['status'] ?? 'all';

// Build query based on role
if ($user_role === 'seller') {
    $query = "
        SELECT 
            ct.*,
            p.name as product_name,
            p.price as unit_price,
            buyer.name as buyer_name,
            buyer.phone as buyer_phone_alt,
            seller.name as seller_name,
            pi.url as product_image
        FROM cod_transactions ct
        INNER JOIN products p ON ct.product_id = p.id
        INNER JOIN accounts buyer ON ct.buyer_id = buyer.id
        INNER JOIN accounts seller ON ct.seller_id = seller.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE ct.seller_id = ?
    ";
} else {
    $query = "
        SELECT 
            ct.*,
            p.name as product_name,
            p.price as unit_price,
            buyer.name as buyer_name,
            seller.name as seller_name,
            seller.phone as seller_phone_alt,
            pi.url as product_image
        FROM cod_transactions ct
        INNER JOIN products p ON ct.product_id = p.id
        INNER JOIN accounts buyer ON ct.buyer_id = buyer.id
        INNER JOIN accounts seller ON ct.seller_id = seller.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE ct.buyer_id = ?
    ";
}

// Add status filter
if ($filter_status !== 'all') {
    $query .= " AND ct.status = ?";
}

$query .= " ORDER BY ct.created_at DESC";

$stmt = $pdo->prepare($query);
if ($filter_status !== 'all') {
    $stmt->execute([$user_id, $filter_status]);
} else {
    $stmt->execute([$user_id]);
}
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count by status
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count 
    FROM cod_transactions 
    WHERE " . ($user_role === 'seller' ? 'seller_id' : 'buyer_id') . " = ?
    GROUP BY status
");
$stmt->execute([$user_id]);
$status_counts = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status_counts[$row['status']] = $row['count'];
}

$total_count = array_sum($status_counts);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COD Transactions — CampusMarket</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/cod.css">
</head>

<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="cod-container">
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>🤝 COD Transactions</h1>
            <p class="subtitle">Transaksi Cash on Delivery - Bayar saat bertemu langsung</p>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
        <a href="?status=all" class="tab-item <?= $filter_status === 'all' ? 'active' : '' ?>">
            Semua (<?= $total_count ?>)
        </a>
        <a href="?status=pending" class="tab-item <?= $filter_status === 'pending' ? 'active' : '' ?>">
            Menunggu (<?= $status_counts['pending'] ?? 0 ?>)
        </a>
        <a href="?status=approved" class="tab-item <?= $filter_status === 'approved' ? 'active' : '' ?>">
            Disetujui (<?= $status_counts['approved'] ?? 0 ?>)
        </a>
        <a href="?status=completed" class="tab-item <?= $filter_status === 'completed' ? 'active' : '' ?>">
            Selesai (<?= $status_counts['completed'] ?? 0 ?>)
        </a>
        <a href="?status=cancelled" class="tab-item <?= $filter_status === 'cancelled' ? 'active' : '' ?>">
            Dibatalkan (<?= $status_counts['cancelled'] ?? 0 ?>)
        </a>
    </div>

    <!-- TRANSACTIONS LIST -->
    <?php if (count($transactions) > 0): ?>
    <div class="transaction-list">
        <?php foreach ($transactions as $trans): ?>
        <div class="transaction-card">
            <!-- Product Image -->
            <div class="transaction-image">
                <?php
                $prod_img = !empty($trans['product_image']) 
                    ? "../../uploads/products/" . $trans['product_image']
                    : "../../assets/images/default-product.png";
                ?>
                <img src="<?= htmlspecialchars($prod_img) ?>" alt="Product">
            </div>
            
            <!-- Transaction Info -->
            <div class="transaction-info">
                <div class="transaction-header">
                    <h3><?= htmlspecialchars($trans['product_name']) ?></h3>
                    <?php
                    $status_class = '';
                    $status_text = '';
                    $status_icon = '';
                    
                    switch($trans['status']) {
                        case 'pending':
                            $status_class = 'status-pending';
                            $status_text = 'Menunggu Konfirmasi';
                            $status_icon = '⏳';
                            break;
                        case 'approved':
                            $status_class = 'status-approved';
                            $status_text = 'Disetujui';
                            $status_icon = '✅';
                            break;
                        case 'completed':
                            $status_class = 'status-completed';
                            $status_text = 'Selesai';
                            $status_icon = '🎉';
                            break;
                        case 'cancelled':
                            $status_class = 'status-cancelled';
                            $status_text = 'Dibatalkan';
                            $status_icon = '❌';
                            break;
                    }
                    ?>
                    <span class="status-badge <?= $status_class ?>">
                        <?= $status_icon ?> <?= $status_text ?>
                    </span>
                </div>
                
                <div class="transaction-details">
                    <div class="detail-row">
                        <span class="label">Jumlah:</span>
                        <span class="value"><?= $trans['qty'] ?> item × Rp <?= number_format($trans['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Total:</span>
                        <span class="value price-total">Rp <?= number_format($trans['total'], 0, ',', '.') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">
                            <?= $user_role === 'seller' ? 'Pembeli:' : 'Penjual:' ?>
                        </span>
                        <span class="value">
                            <?= htmlspecialchars($user_role === 'seller' ? $trans['buyer_name'] : $trans['seller_name']) ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="label">No. Telp:</span>
                        <span class="value">
                            <?= htmlspecialchars($user_role === 'seller' ? $trans['buyer_phone'] : $trans['seller_phone']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="meeting-details">
                    <h4>📍 Titik Temu</h4>
                    <p class="meeting-location"><?= htmlspecialchars($trans['meeting_location']) ?></p>
                    <p class="meeting-time">
                        🕒 <?= date('l, d F Y • H:i', strtotime($trans['meeting_time'])) ?> WIB
                    </p>
                </div>
                
                <div class="transaction-date">
                    Dibuat: <?= date('d M Y, H:i', strtotime($trans['created_at'])) ?>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="transaction-actions">
                <?php if ($user_role === 'seller' && $trans['status'] === 'pending'): ?>
                    <button class="btn btn-approve" onclick="updateStatus(<?= $trans['id'] ?>, 'approved')">
                        ✅ Setujui
                    </button>
                    <button class="btn btn-reject" onclick="updateStatus(<?= $trans['id'] ?>, 'cancelled')">
                        ❌ Tolak
                    </button>
                <?php elseif ($trans['status'] === 'approved'): ?>
                    <button class="btn btn-complete" onclick="updateStatus(<?= $trans['id'] ?>, 'completed')">
                        ✔️ Tandai Selesai
                    </button>
                <?php endif; ?>
                
                <?php if ($trans['status'] === 'pending' || $trans['status'] === 'approved'): ?>
                    <a href="chat_transaction.php?id=<?= $trans['id'] ?>" class="btn btn-chat">
                        💬 Chat
                    </a>
                <?php endif; ?>
                
                <a href="detail.php?id=<?= $trans['id'] ?>" class="btn btn-detail">
                    📄 Detail
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>Belum ada transaksi COD</h3>
        <p>
            <?php if ($filter_status === 'all'): ?>
                Transaksi COD Anda akan muncul di sini
            <?php else: ?>
                Tidak ada transaksi dengan status <?= ucfirst($filter_status) ?>
            <?php endif; ?>
        </p>
        <a href="../../public/index.php" class="btn btn-primary">
            🛍️ Mulai Belanja
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(transactionId, newStatus) {
    if (!confirm('Apakah Anda yakin ingin mengubah status transaksi ini?')) {
        return;
    }
    
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `transaction_id=${transactionId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}
</script>

</body>
</html>