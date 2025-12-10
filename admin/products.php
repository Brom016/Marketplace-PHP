<?php
require_once __DIR__ . '/../includes/config.php';
include "header.php";

$products = $pdo->query("
    SELECT p.*, a.name AS seller_name 
    FROM products p 
    LEFT JOIN accounts a ON p.seller_id = a.id
")->fetchAll();
?>
<link rel="stylesheet" href="../assets/css/admin_system.css">
<h2 style="margin: 20px 0;">Products Management</h2>

<table class="user-table">
    <tr>
        <th>ID</th>
        <th>Seller</th>
        <th>Title</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['seller_name']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td><?= $p['stock'] ?></td>
            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="action-btn btn-edit">Edit</a>
                <a href="delete_product.php?id=<?= $p['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Hapus produk?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
