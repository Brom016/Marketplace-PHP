<?php
require_once __DIR__ . "/../../includes/config.php";

// cek login
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'seller') {
    die("Akses ditolak!");
}

$seller_id = $user['id'];

$products = $pdo->prepare("
    SELECT p.*, 
        (SELECT url FROM product_images 
         WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS thumb
    FROM products p
    WHERE p.seller_id = ?
    ORDER BY p.id DESC
");
$products->execute([$seller_id]);
$products = $products->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Produk Saya</title></head>
<body>

<h2>Produk Saya</h2>

<a href="product_add.php">➕ Tambah Produk</a>
<br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($products as $p): ?>
    <tr>
        <td>
            <?php if ($p['thumb']): ?>
                <img src="../../uploads/products/<?= $p['thumb'] ?>" width="80">
            <?php else: ?>
                <i>Tidak ada gambar</i>
            <?php endif; ?>
        </td>

        <td><?= $p['name'] ?></td>
        <td><?= number_format($p['price'], 0, ',', '.') ?></td>
        <td><?= $p['stock'] ?></td>

        <td>
            <a href="product_edit.php?id=<?= $p['id'] ?>">Edit</a> |
            <a href="product_delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus produk?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
