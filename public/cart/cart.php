<?php
require_once __DIR__ . '/../../includes/config.php';

/* ======================
   AUTH
====================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* USER */
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* PROFILE PIC */
$profile_pic = "../../assets/img/default-profile.png";
if (!empty($user['profile_picture'])) {
    $profile_pic = "../../uploads/profile/" . $user['profile_picture'];
}

/* CART DATA */
$stmt = $pdo->prepare("
    SELECT 
        w.id AS cart_id,
        w.quantity,
        p.id AS product_id,
        p.name,
        p.price,
        p.stock,
        p.seller_id,
        (
            SELECT pi.url
            FROM product_images pi
            WHERE pi.product_id = p.id
            ORDER BY pi.is_primary DESC, pi.id ASC
            LIMIT 1
        ) AS image
    FROM wishes w
    JOIN products p ON p.id = w.product_id
    WHERE w.user_id = ?
");
$stmt->execute([$user_id]);
$carts = $stmt->fetchAll();

/* HEADER COUNTER */
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

/* LOGO */
$logoA = "../assets/images/icontrs.png";
$logoB = "../../assets/images/icontrs.png";
$logo  = file_exists($logoA) ? $logoA : $logoB;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang</title>

    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/cart.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- HEADER -->
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
                    <?php if ($cart_total): ?>
                        <span class="cm-badge green"><?= $cart_total ?></span>
                    <?php endif; ?>
                </a>

                <a href="../chat/index.php" class="cm-icon-btn">
                    <i class="fa-solid fa-message"></i>
                    <?php if ($chat_total): ?>
                        <span class="cm-badge"><?= $chat_total ?></span>
                    <?php endif; ?>
                </a>

                <div class="cm-profile" onclick="window.location='../profile/profile.php'">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" class="cm-profile-img">
                    <div class="cm-user-info">
                        <span><?= htmlspecialchars($user['name']) ?></span>
                        <small>Mahasiswa</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- CART -->
    <div class="cart-container">

        <h2 class="cart-title">Produk di Keranjang</h2>

        <div class="cart-header">
            <div>Produk</div>
            <div></div>
            <div>Harga</div>
            <div>Qty</div>
            <div>Total</div>
            <div>Chat</div>
            <div></div>
        </div>

        <?php if (!$carts): ?>
            <p class="empty-cart">Keranjang kosong</p>
        <?php endif; ?>

        <?php foreach ($carts as $c):
            $subtotal = $c['price'] * $c['quantity'];
            $img = $c['image']
                ? "../../uploads/products/" . $c['image']
                : "../../assets/img/no-image.png";
        ?>
            <div class="cart-item">

                <img src="<?= htmlspecialchars($img) ?>">

                <div>
                    <b><?= htmlspecialchars($c['name']) ?></b><br>
                    <small>Stok: <?= $c['stock'] ?></small>
                </div>

                <div>Rp <?= number_format($c['price']) ?></div>

                <div>
                    <?php if ($c['stock'] <= 0): ?>
                        <span class="out">Kosong</span>
                    <?php else: ?>
                        <form action="cart_update.php" method="post" class="qty-box">
                            <input type="hidden" name="cart_id" value="<?= $c['cart_id'] ?>">
                            <button name="action" value="minus">−</button>
                            <span><?= $c['quantity'] ?></span>
                            <button name="action" value="plus" <?= $c['quantity'] >= $c['stock'] ? 'disabled' : '' ?>>+</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="subtotal">
                    Rp <?= number_format($subtotal) ?>
                </div>

                <?php if ($c['seller_id'] != $user_id): ?>
                    <a href="../chat/index.php?user=<?= $c['seller_id'] ?>" class="chat-btn">
                        💬 Chat Penjual
                    </a>
                <?php else: ?>
                    <span class="own-product">Produk Anda</span>
                <?php endif; ?>

                <form action="cart_delete.php" method="post">
                    <input type="hidden" name="cart_id" value="<?= $c['cart_id'] ?>">

                    <div class="delete-wrapper">
                        <button class="delete-btn" title="Hapus dari keranjang">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </form>


            </div>
        <?php endforeach; ?>

    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>

</html>