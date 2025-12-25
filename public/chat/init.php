<?php
require_once "../../includes/config.php";
require_once "../../includes/functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        cr.id,
        cr.product_id,
        p.name AS product_name
    FROM chat_rooms cr
    JOIN products p ON p.id = cr.product_id
    WHERE cr.buyer_id = ? OR cr.seller_id = ?
    ORDER BY cr.updated_at DESC
");
$stmt->execute([$user_id, $user_id]);
$rooms = $stmt->fetchAll();
?>

<?php include "../components/header.php"; ?>

<div style="max-width:800px;margin:20px auto">
    <h3>Chat Saya</h3>

    <?php if (!$rooms): ?>
        <p>Belum ada chat</p>
    <?php endif; ?>

    <?php foreach ($rooms as $r): ?>
        <div style="padding:10px;border:1px solid #ddd;margin-bottom:5px">
            <b><?= htmlspecialchars($r['product_name']) ?></b><br>
            <a href="index.php?room=<?= $r['id'] ?>">Buka Chat</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include "../components/footer.php"; ?>