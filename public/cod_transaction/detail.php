<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];
$tx_id = $_GET['id'] ?? 0;

if (!$tx_id) {
    header("Location: index.php");
    exit;
}

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
   QUERY TRANSAKSI
======================= */
$stmt = $pdo->prepare("
    SELECT 
        ct.*,
        p.name AS product_name,
        (SELECT url FROM product_images 
         WHERE product_id = p.id 
         ORDER BY is_primary DESC LIMIT 1) AS product_image,
        b.name AS buyer_name,
        s.name AS seller_name
    FROM cod_transactions ct
    JOIN products p ON p.id = ct.product_id
    JOIN accounts b ON b.id = ct.buyer_id
    JOIN accounts s ON s.id = ct.seller_id
    WHERE ct.id = ?
      AND (ct.buyer_id = ? OR ct.seller_id = ?)
    LIMIT 1
");

$stmt->execute([$tx_id, $user_id, $user_id]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tx) {
    die("Akses ditolak.");
}

$is_buyer = $tx['buyer_id'] == $user_id;
$partner_name  = $is_buyer ? $tx['seller_name'] : $tx['buyer_name'];
$partner_phone = $is_buyer ? $tx['seller_phone'] : $tx['buyer_phone'];

function badgeStatus($status)
{
    $map = [
        'pending' => 'badge-pending',
        'waiting_buyer' => 'badge-waiting',
        'completed' => 'badge-completed',
        'cancelled' => 'badge-cancelled',
        'rejected' => 'badge-rejected'
    ];
    $cls = $map[$status] ?? 'badge-default';
    return "<span class='badge {$cls}'>" . strtoupper($status) . "</span>";
}

include "../components/header.php";
?>

<link rel="stylesheet" href="detail.css">

<div class="detail-container">

    <a href="index.php" class="back-link">&larr; Kembali</a>

    <div class="detail-card">

        <!-- HEADER -->
        <div class="detail-header">
            <div>
                <h2>Transaksi #<?= $tx['id'] ?></h2>
                <small>Dibuat: <?= formatTanggalIndo($tx['created_at']) ?></small>
            </div>
            <?= badgeStatus($tx['status']) ?>
        </div>

        <!-- BODY -->
        <div class="detail-body">

            <!-- LEFT -->
            <div>
                <div class="product-preview">
                    <?php
                    $img = $tx['product_image']
                        ? "../../uploads/products/" . $tx['product_image']
                        : "../../assets/images/no-image.png";
                    ?>
                    <img src="<?= $img ?>" alt="Produk">

                    <div>
                        <strong><?= htmlspecialchars($tx['product_name']) ?></strong>
                        <div>Qty: <?= $tx['qty'] ?></div>
                    </div>
                </div>

                <div class="info-group">
                    <label>Total COD</label>
                    <div class="price">
                        Rp <?= number_format($tx['total'], 0, ',', '.') ?>
                    </div>
                </div>

                <div class="info-group">
                    <label><?= $is_buyer ? 'Penjual' : 'Pembeli' ?></label>
                    <div>
                        <?= htmlspecialchars($partner_name) ?><br>
                        <a target="_blank"
                           href="https://wa.me/<?= preg_replace('/^0/', '62', $partner_phone) ?>">
                            <?= $partner_phone ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="meeting-box">
                <h3>Detail Pertemuan</h3>

                <div class="info-group">
                    <label>Lokasi</label>
                    <div><?= $tx['meeting_location'] ?: '<i>Belum disepakati</i>' ?></div>
                </div>

                <div class="info-group">
                    <label>Waktu</label>
                    <div>
                        <?= $tx['meeting_time']
                            ? formatTanggalIndo($tx['meeting_time']) . ' WIB'
                            : '<i>Belum disepakati</i>' ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="detail-footer">
            <button onclick="window.print()">Cetak Bukti</button>
        </div>

    </div>
</div>

<?php include "../components/footer.php"; ?>
