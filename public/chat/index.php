<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

// 1. SIAPKAN DATA USER UNTUK HEADER
$user_id_session = $_SESSION['user_id'];
$stmtUser = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmtUser->execute([$user_id_session]);
$user = $stmtUser->fetch();

// Atur Foto Profil
$profile_pic = !empty($user['profile_picture'])
    ? "../../uploads/profile/" . $user['profile_picture']
    : "../../assets/images/default-user.png";


// Ambil Room ID
$room_id = isset($_GET['room']) ? (int)$_GET['room'] : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   

</head>

<body>

    <!-- ======================================================
   HEADER SECTION
====================================================== -->
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

    <div class="cm-header">
        <div class="cm-container">

            <!-- LOGO -->
            <a href="../index.php" class="cm-logo">
                <img src="<?= $logo ?>" alt="Logo" onerror="this.src='../../assets/images/logo-placeholder.png'">
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
                        <span class="cm-badge"><?= $notif_total ?></span>
                    <?php endif; ?>
                </a>

                <?php
                $current_page     = basename($_SERVER['PHP_SELF']);
                $is_profile_page  = ($current_page === 'profile.php');
                ?>

                <?php if (!$user): ?>
                    <div class="cm-auth">
                        <a href="../login.php">Sign In</a> |
                        <a href="../register.php">Sign Up</a>
                    </div>

                <?php else: ?>
                    <div
                        class="cm-profile"
                        <?php if (!$is_profile_page): ?> onclick="window.location='../profile.php'" <?php endif; ?>>
                        <img
                            src="<?= htmlspecialchars($profile_pic) ?>"
                            class="cm-profile-img"
                            onerror="this.src='../../assets/images/default-user.png'">

                        <div class="cm-user-info">
                            <span><?= htmlspecialchars(substr($user['name'], 0, 10)) ?></span>
                            <small>Mahasiswa</small>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- HEADER END -->

    <!-- ======================================================
   MAIN CONTENT
====================================================== -->
    <div class="container-main">

        <?php
        /* ======================================================
       MODE 1 : LIST CHAT 
    ====================================================== */
        if ($room_id <= 0):

            $stmt = $pdo->prepare("
    SELECT 
        cr.id,
        cr.product_id,
        p.name AS product_name,

        CASE 
            WHEN cr.buyer_id = :uid THEN s.name
            ELSE b.name
        END AS other_name,

        CASE 
            WHEN cr.buyer_id = :uid THEN s.profile_picture
            ELSE b.profile_picture
        END AS other_photo,

        (
            SELECT cm.message
            FROM chat_messages cm
            WHERE cm.room_id = cr.id
            ORDER BY cm.created_at DESC
            LIMIT 1
        ) AS last_message,

        (
            SELECT cm.created_at
            FROM chat_messages cm
            WHERE cm.room_id = cr.id
            ORDER BY cm.created_at DESC
            LIMIT 1
        ) AS last_time

    FROM chat_rooms cr
    JOIN products p ON p.id = cr.product_id
    JOIN accounts b ON b.id = cr.buyer_id
    JOIN accounts s ON s.id = cr.seller_id
    WHERE cr.buyer_id = :uid OR cr.seller_id = :uid
    ORDER BY last_time DESC
");
            $stmt->execute(['uid' => $user_id]);
            $rooms = $stmt->fetchAll();



        ?>

            <div class="chat-box" style="max-width:100%">
                <h3>Chat Saya</h3>
                <br>
                <br>

                <?php if (!$rooms): ?>
                    <p style="text-align:center; color:#888;">Belum ada chat</p>
                <?php endif; ?>

                <?php foreach ($rooms as $r): ?>

                    <?php

                    $avatar = !empty($r['other_photo'])
                        ? "../../uploads/profile/" . $r['other_photo']
                        : "../../assets/images/default-user.png";
                    ?>

                    <a href="index.php?room=<?= $r['id'] ?>" class="chat-item">

                        <img
                            src="<?= htmlspecialchars($avatar) ?>"
                            class="chat-avatar"
                            onerror="this.src='../../assets/images/default-user.png'">

                        <div class="chat-content">

                            <div class="chat-top-row">
                                <span class="chat-name">
                                    <?= htmlspecialchars($r['other_name']) ?>
                                </span>
                                <span class="chat-time">
                                    <?= $r['last_time']
                                        ? date('H:i', strtotime($r['last_time']))
                                        : ''
                                    ?>
                                </span>
                            </div>

                            <!-- PREVIEW PESAN -->
                            <div class="chat-preview">
                                <?= $r['last_message']
                                    ? htmlspecialchars(mb_strimwidth($r['last_message'], 0, 45, '...'))
                                    : '<i>Belum ada pesan</i>'
                                ?>
                            </div>

                            <!-- NAMA PRODUK -->
                            <div class="chat-product">
                                📦 <?= htmlspecialchars(mb_strimwidth($r['product_name'], 0, 40, '...')) ?>
                            </div>

                        </div>
                    </a>


                <?php endforeach; ?>


            </div>

            <?php
        /* ======================================================
       MODE 2 : DETAIL CHAT
    ====================================================== */
        else:

            /* ambil room */
            $stmt = $pdo->prepare("SELECT * FROM chat_rooms WHERE id = ?");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch();

            if (!$room || ($user_id != $room['buyer_id'] && $user_id != $room['seller_id'])) {
                echo "<div class='chat-box'><h3>Akses Ditolak</h3></div>";
            } else {
                $is_buyer = ($user_id == $room['buyer_id']);
                $cod = null;

                /* AMBIL DATA COD YANG SEDANG BERJALAN */
                $stmt = $pdo->prepare("
                SELECT * FROM cod_transactions
                WHERE product_id = ? 
                  AND ((buyer_id = ? AND seller_id = ?) OR (buyer_id = ? AND seller_id = ?))
                  AND status NOT IN ('cancelled', 'rejected', 'completed')
                ORDER BY id DESC LIMIT 1
            ");
                $stmt->execute([
                    $room['product_id'],
                    $room['buyer_id'],
                    $room['seller_id'],
                    $room['buyer_id'],
                    $room['seller_id']
                ]);
                $cod = $stmt->fetch(PDO::FETCH_ASSOC);
                $has_cod = ($cod) ? true : false;

                /* ambil produk */
                $stmt = $pdo->prepare("
                SELECT p.name, p.price,
                (SELECT url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS image
                FROM products p WHERE p.id = ?
            ");
                $stmt->execute([$room['product_id']]);
                $product = $stmt->fetch();

                /* ambil chat */
                $stmt = $pdo->prepare("
                SELECT cm.*, a.name AS sender_name
                FROM chat_messages cm
                LEFT JOIN accounts a ON a.id = cm.sender_id
                WHERE cm.room_id = ? ORDER BY cm.created_at ASC
            ");
                $stmt->execute([$room_id]);
                $messages = $stmt->fetchAll();

                /* tandai pesan dibaca */
                $stmt = $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE room_id = ? AND receiver_id = ?");
                $stmt->execute([$room_id, $user_id]);
            ?>

                <div class="chat-box">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3>Chat Produk: <?= htmlspecialchars($product['name']) ?></h3>
                        <a href="index.php" class="btn btn-orange" style="text-decoration:none;">Kembali</a>
                    </div>

                    <!-- PREVIEW PRODUK -->
                    <?php if ($product): ?>
                        <div class="product-preview">
                            <img src="<?= $product['image'] ? '../../uploads/products/' . $product['image'] : '../../assets/img/default-product.jpg' ?>">
                            <div>
                                <b><?= htmlspecialchars($product['name']) ?></b><br>
                                <span style="color:#4caf50; font-weight:bold;">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- CHAT BUBBLES -->
                    <div style="max-height: 400px; overflow-y: auto; padding-right:5px; margin-bottom:15px;">
                        <?php foreach ($messages as $m):
                            if ($m['sender_id'] == $user_id) {
                                $cls = 'me ' . ($is_buyer ? 'buyer' : 'seller');
                            } else {
                                $cls = 'other';
                            }

                        ?>
                            <div class="bubble <?= $cls ?>">
                                <div class="sender"><?= htmlspecialchars($m['sender_name'] ?? 'System') ?></div>
                                <div><?= htmlspecialchars($m['message']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- FORM KIRIM -->
                    <form method="post" action="send.php" style="display:flex; gap:10px;">
                        <input type="hidden" name="room_id" value="<?= $room_id ?>">
                        <input name="message" required style="flex-grow:1; padding:10px; border:1px solid #ccc; border-radius:6px;" placeholder="Tulis pesan...">
                        <button class="btn btn-green"><i class="fas fa-paper-plane"></i></button>
                    </form>

                    <!-- AJUKAN COD -->
                    <?php if ($is_buyer && !$has_cod): ?>
                        <form method="post" action="../cod_transaction/request.php" style="margin-top:15px; text-align:right;">
                            <input type="hidden" name="room_id" value="<?= $room_id ?>">
                            <button class="btn btn-orange">Ajukan COD</button>
                        </form>
                    <?php endif; ?>

                    <!-- RESPON COD (SELLER) -->
                    <?php if (isset($cod) && $cod && !$is_buyer && $cod['status'] === 'pending'): ?>
                        <div class="cod-panel">
                            <h4>Permintaan COD</h4>
                            <form method="post" action="../cod_transaction/approve.php">
                                <input type="hidden" name="cod_id" value="<?= $cod['id'] ?>">
                                <label>Harga Final</label>
                                <input type="number" name="price" value="<?= $product['price'] ?>" required>
                                <label>Lokasi COD</label>
                                <input type="text" name="meeting_location" required>
                                <label>Waktu COD</label>
                                <input type="datetime-local" name="meeting_time" required>

                                <button class="btn btn-green">Setujui COD</button>
                                <button formaction="../cod_transaction/reject.php" class="btn btn-red">Tolak COD</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- KONFIRMASI COD (BUYER) -->
                    <?php if ($cod && $is_buyer && in_array($cod['status'], ['approved', 'waiting_buyer'])): ?>
                        <div class="cod-panel">
                            <h4>Konfirmasi COD (Buyer)</h4>
                            <p>
                                <b>Harga Final:</b> Rp <?= number_format($cod['price'], 0, ',', '.') ?><br>
                                <b>Lokasi:</b> <?= htmlspecialchars($cod['meeting_location']) ?><br>
                                <b>Waktu:</b> <?= htmlspecialchars($cod['meeting_time']) ?>
                            </p>
                            <form method="post" action="../cod_transaction/complete.php" style="display:inline">
                                <input type="hidden" name="cod_id" value="<?= $cod['id'] ?>">
                                <button class="btn btn-green">Barang Diterima</button>
                            </form>
                            <form method="post" action="../cod_transaction/cancel.php" style="display:inline">
                                <input type="hidden" name="cod_id" value="<?= $cod['id'] ?>">
                                <button class="btn btn-red">Batalkan COD</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                </div>

        <?php
            } // End Access Check
        endif; // End Mode Check 
        ?>

    </div> <!-- End Container Main -->

    <?php include "../components/footer.php"; ?>

</body>

</html>