<?php
require_once __DIR__ . '/../includes/config.php';
include "header.php";

$cod = $pdo->query("
    SELECT 
        c.*,
        a.name AS buyer,
        s.name AS seller,
        p.name AS product_name,
        pi.url AS product_image
    FROM cod_transactions c
    LEFT JOIN accounts a ON c.buyer_id = a.id
    LEFT JOIN accounts s ON c.seller_id = s.id
    LEFT JOIN products p ON c.product_id = p.id
    LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
")->fetchAll();
?>
<link rel="stylesheet" href="../assets/css/admin_system.css">
<h2 style="margin: 20px 0;">COD Transactions</h2>

<table class="user-table">
    <tr>
        <th>ID</th>
        <th>Buyer</th>
        <th>Seller</th>
        <th>Product</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    <?php foreach ($cod as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['buyer']) ?></td>
            <td><?= htmlspecialchars($c['seller']) ?></td>

            <td>
                <?php if ($c['product_image']): ?>
                    <img src="../uploads/products/<?= htmlspecialchars($c['product_image']) ?>" class="product-thumb">
                <?php else: ?>
                    <span>No image</span>
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= $c['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
