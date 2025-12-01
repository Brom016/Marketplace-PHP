<?php
// root/public/cod_transaction/create.php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$product_id = $_GET['product_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get product details
$stmt = $pdo->prepare("
    SELECT p.*, a.name as seller_name, a.phone as seller_phone, pi.url as image
    FROM products p
    INNER JOIN accounts a ON p.seller_id = a.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ../index.php");
    exit;
}

// Check if user is trying to buy their own product
if ($product['seller_id'] == $user_id) {
    $_SESSION['error'] = "Anda tidak dapat membeli produk Anda sendiri";
    header("Location: ../products/detail.php?id=" . $product_id);
    exit;
}

$prod_img = !empty($product['image']) 
    ? "../../uploads/products/" . $product['image']
    : "../../assets/images/default-product.png";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = intval($_POST['qty'] ?? 1);
    $meeting_location = trim($_POST['meeting_location'] ?? '');
    $meeting_date = $_POST['meeting_date'] ?? '';
    $meeting_time = $_POST['meeting_time'] ?? '';
    $buyer_phone = trim($_POST['buyer_phone'] ?? '');
    
    $errors = [];
    
    if ($qty < 1) {
        $errors[] = "Jumlah minimal 1";
    }
    
    if ($qty > $product['stock']) {
        $errors[] = "Stok tidak mencukupi";
    }
    
    if (empty($meeting_location)) {
        $errors[] = "Lokasi pertemuan harus diisi";
    }
    
    if (empty($meeting_date) || empty($meeting_time)) {
        $errors[] = "Tanggal dan waktu pertemuan harus diisi";
    }
    
    if (empty($buyer_phone)) {
        $errors[] = "Nomor telepon harus diisi";
    }
    
    if (empty($errors)) {
        $meeting_datetime = $meeting_date . ' ' . $meeting_time;
        $total = $qty * $product['price'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO cod_transactions 
                (buyer_id, seller_id, product_id, qty, price, total, meeting_location, meeting_time, buyer_phone, seller_phone, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $user_id,
                $product['seller_id'],
                $product_id,
                $qty,
                $product['price'],
                $total,
                $meeting_location,
                $meeting_datetime,
                $buyer_phone,
                $product['seller_phone']
            ]);
            
            $transaction_id = $pdo->lastInsertId();
            
            // Create notification for seller
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, title, message, link, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $product['seller_id'],
                'Transaksi COD Baru',
                "Ada permintaan COD baru untuk produk '{$product['name']}'",
                '../cod_transaction/detail.php?id=' . $transaction_id
            ]);
            
            $_SESSION['success'] = "Permintaan COD berhasil dibuat. Menunggu konfirmasi penjual.";
            header("Location: detail.php?id=" . $transaction_id);
            exit;
            
        } catch (Exception $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Transaksi COD — CampusMarket</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        body {
            background: #f4f4f4;
        }
        
        .create-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .create-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        
        .create-card h2 {
            margin: 0 0 25px 0;
            font-size: 26px;
        }
        
        .product-preview {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .product-preview-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .product-preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-preview-info h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
        }
        
        .product-preview-price {
            color: #10b981;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .product-preview-seller {
            font-size: 14px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #10b981;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        .total-section {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            border: 2px solid #10b981;
        }
        
        .total-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .total-price {
            font-size: 32px;
            font-weight: bold;
            color: #10b981;
        }
        
        .submit-button {
            width: 100%;
            padding: 15px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
        }
        
        .submit-button:hover {
            background: #059669;
        }
        
        .info-box {
            background: #dbeafe;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 25px;
            font-size: 14px;
            color: #1e40af;
        }
    </style>
</head>

<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="create-container">
    <a href="../products/detail.php?id=<?= $product_id ?>" class="back-link">
        ← Kembali ke Produk
    </a>
    
    <div class="create-card">
        <h2>🤝 Buat Transaksi COD</h2>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Error:</strong><br>
            <?php foreach ($errors as $error): ?>
                • <?= htmlspecialchars($error) ?><br>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="info-box">
            <strong>ℹ️ Tentang COD (Cash on Delivery):</strong><br>
            Pembayaran dilakukan saat bertemu langsung dengan penjual. Pastikan lokasi dan waktu pertemuan sesuai kesepakatan.
        </div>
        
        <!-- Product Preview -->
        <div class="product-preview">
            <div class="product-preview-image">
                <img src="<?= htmlspecialchars($prod_img) ?>" alt="Product">
            </div>
            <div class="product-preview-info">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <div class="product-preview-price">
                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                </div>
                <div class="product-preview-seller">
                    Penjual: <?= htmlspecialchars($product['seller_name']) ?>
                </div>
                <div class="product-preview-seller">
                    Stok: <?= $product['stock'] ?> tersedia
                </div>
            </div>
        </div>
        
        <!-- Form -->
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Jumlah Pesanan</label>
                <input type="number" name="qty" class="form-input" value="1" min="1" max="<?= $product['stock'] ?>" required id="qty-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Lokasi Pertemuan</label>
                <input type="text" name="meeting_location" class="form-input" placeholder="Contoh: Kantin Fakultas Teknik" required>
                <small style="color: #666; font-size: 13px; display: block; margin-top: 5px;">
                    Tentukan lokasi pertemuan yang mudah dijangkau
                </small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Pertemuan</label>
                    <input type="date" name="meeting_date" class="form-input" min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Waktu Pertemuan</label>
                    <input type="time" name="meeting_time" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Nomor Telepon Anda</label>
                <input type="tel" name="buyer_phone" class="form-input" placeholder="08123456789" required>
                <small style="color: #666; font-size: 13px; display: block; margin-top: 5px;">
                    Nomor yang bisa dihubungi penjual
                </small>
            </div>
            
            <!-- Total -->
            <div class="total-section">
                <div class="total-label">Total Pembayaran (COD)</div>
                <div class="total-price" id="total-price">
                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                </div>
                <small style="color: #059669; display: block; margin-top: 5px;">
                    Bayar saat bertemu dengan penjual
                </small>
            </div>
            
            <button type="submit" class="submit-button">
                Buat Permintaan COD
            </button>
        </form>
    </div>
</div>

<script>
const qtyInput = document.getElementById('qty-input');
const totalPrice = document.getElementById('total-price');
const unitPrice = <?= $product['price'] ?>;

qtyInput.addEventListener('input', function() {
    const qty = parseInt(this.value) || 1;
    const total = qty * unitPrice;
    totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
});
</script>

</body>
</html>