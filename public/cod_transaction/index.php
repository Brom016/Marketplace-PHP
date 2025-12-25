<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

/* =======================
   USER SESSION
======================= */
$user_id = $_SESSION['user_id'];

/* =======================
   AMBIL DATA USER LOGIN
   (SESUAI DATABASE)
======================= */
$stmtUser = $pdo->prepare("
    SELECT id, name, role
    FROM accounts
    WHERE id = ?
");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // safety net, harusnya gak kejadian
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* =======================
   PROFILE PIC (STATIC)
   DATABASE TIDAK PUNYA
======================= */
$profile_pic = "../../assets/images/default-user.png";

/* =======================
   HITUNG WISHLIST
======================= */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishes WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_total = (int)$stmt->fetchColumn();

/* =======================
   HITUNG NOTIFIKASI
======================= */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM notifications 
    WHERE user_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$notif_total = (int)$stmt->fetchColumn();

/* =======================
   HITUNG CHAT BELUM DIBACA
======================= */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM chat_messages 
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$chat_total = (int)$stmt->fetchColumn();

/* =======================
   HELPER TANGGAL INDONESIA
======================= */
function formatTanggalIndo($datetime)
{
    if (!$datetime) return '-';

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    ];

    $ts = strtotime($datetime);

    return date('d', $ts) . ' ' .
        $bulan[(int)date('m', $ts)] . ' ' .
        date('Y, H:i', $ts);
}

/* =======================
   QUERY TRANSAKSI COD
======================= */
$stmt = $pdo->prepare("
    SELECT 
        ct.*, 
        p.name AS product_name,
        b.name AS buyer_name,
        s.name AS seller_name
    FROM cod_transactions ct
    JOIN products p ON p.id = ct.product_id
    JOIN accounts b ON b.id = ct.buyer_id
    JOIN accounts s ON s.id = ct.seller_id
    WHERE ct.buyer_id = ? OR ct.seller_id = ?
    ORDER BY ct.created_at DESC
");
$stmt->execute([$user_id, $user_id]);
$all_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =======================
   STATUS BADGE
======================= */
function getStatusBadge($status)
{
    $map = [
        'pending'        => ['Menunggu Konfirmasi', 'pending'],
        'waiting_buyer'  => ['Menunggu Pembeli', 'waiting'],
        'completed'      => ['Selesai', 'completed'],
        'cancelled'      => ['Dibatalkan', 'cancelled'],
        'rejected'       => ['Ditolak', 'rejected']
    ];

    $s = $map[$status] ?? [$status, 'default'];
    return "<span class='status-badge {$s[1]}'>{$s[0]}</span>";
}
?>

<link rel="stylesheet" href="detail.css">
<link rel="stylesheet" href="../../assets/css/header.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- ================= HEADER ================= -->
<div class="cm-header">
    <div class="cm-container">

        <!-- LOGO -->
        <a href="../index.php" class="cm-logo">
            <img src="../../assets/images/icontrs.png" alt="Logo">
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

            <!-- WISHLIST -->
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

            <!-- NOTIFICATION -->
            <a href="../notifications/" class="cm-icon-btn">
                <i class=""></i>
                <?php if ($notif_total > 0): ?>
                    <span class="cm-badge"><?= $notif_total ?></span>
                <?php endif; ?>
            </a>

            <!-- PROFILE -->
            <div class="cm-profile" onclick="window.location='../profile.php'">
                <img src="<?= $profile_pic ?>" class="cm-profile-img">
                <div class="cm-user-info">
                    <span><?= htmlspecialchars(substr($user['name'], 0, 10)) ?></span>
                    <small><?= htmlspecialchars(ucfirst($user['role'])) ?></small>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- =============== END HEADER =============== -->

<div class="container">

    <div class="page-header">
        <h2>Riwayat Transaksi COD</h2>
    </div>

    <a href="../profile/profile.php" class="back-link">&larr; Kembali</a>

    <div class="card">

        <?php if (!$all_transactions): ?>
            <div class="empty-state">
                Belum ada riwayat transaksi COD.
            </div>
        <?php else: ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Produk & Waktu</th>
                        <th>Peran</th>
                        <th>Lawan Transaksi</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($all_transactions as $tx):
                    $is_buyer = ($tx['buyer_id'] == $user_id);
                ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($tx['product_name']) ?></strong><br>
                            <small><?= formatTanggalIndo($tx['created_at']) ?></small>
                        </td>

                        <td>
                            <span class="role-badge <?= $is_buyer ? 'buyer' : 'seller' ?>">
                                <?= $is_buyer ? 'PEMBELI' : 'PENJUAL' ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($is_buyer ? $tx['seller_name'] : $tx['buyer_name']) ?>
                        </td>

                        <td class="price">
                            Rp <?= number_format($tx['total'], 0, ',', '.') ?>
                        </td>

                        <td><?= getStatusBadge($tx['status']) ?></td>

                        <td>
                            <a href="detail.php?id=<?= $tx['id'] ?>" class="btn-detail">
                                Detail →
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>
</div>

<?php include "../components/footer.php"; ?>
