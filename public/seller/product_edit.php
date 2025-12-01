<?php
require_once __DIR__ . "/../../includes/config.php";

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'seller') {
    die("Akses ditolak!");
}

$seller_id = $user['id'];
$id = $_GET['id'];

// ambil produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=? AND seller_id=?");
$stmt->execute([$id, $seller_id]);
$product = $stmt->fetch();

if (!$product) die("Produk tidak ditemukan!");

// ambil gambar
$imgs = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$imgs->execute([$id]);
$imgs = $imgs->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];

    $stmt = $pdo->prepare("
        UPDATE products SET name=?, price=?, stock=?, description=?
        WHERE id=? AND seller_id=?
    ");
    $stmt->execute([$name, $price, $stock, $desc, $id, $seller_id]);

    // Upload gambar baru
    if (!empty($_FILES['images']['name'][0])) {

        $uploadDir = __DIR__ . "/../../uploads/products/";

        foreach ($_FILES['images']['name'] as $i => $img) {
            $tmp = $_FILES['images']['tmp_name'][$i];
            $ext = pathinfo($img, PATHINFO_EXTENSION);
            $newName = "product_{$id}_" . time() . "_$i.$ext";

            move_uploaded_file($tmp, $uploadDir . $newName);

            $pdo->prepare("
                INSERT INTO product_images (product_id, url, is_primary) VALUES (?, ?, 0)
            ")->execute([$id, $newName]);
        }
    }

    header("Location: product_edit.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Produk</title></head>
<body>

<h2>Edit Produk</h2>

<form method="post" enctype="multipart/form-data">

    Nama:<br>
    <input type="text" name="name" value="<?= $product['name'] ?>" required><br><br>

    Harga:<br>
    <input type="number" name="price" value="<?= $product['price'] ?>" required><br><br>

    Stok:<br>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br><br>

    Deskripsi:<br>
    <textarea name="description"><?= $product['description'] ?></textarea><br><br>

    Gambar Baru:<br>
    <input type="file" name="images[]" multiple><br><br>

    <button type="submit">Update</button>
</form>


<h3>Gambar Saat Ini</h3>

<?php foreach ($imgs as $img): ?>
    <div style="margin:10px 0;">
        <img src="../../uploads/products/<?= $img['url'] ?>" width="120">

        <?php if ($img['is_primary'] == 1): ?>
            <b> (PRIMARY) </b>
        <?php else: ?>
            <a href="product_set_primary.php?id=<?= $img['id'] ?>&pid=<?= $id ?>">Jadikan Utama</a>
        <?php endif; ?>

        | 
        <a href="product_image_delete.php?id=<?= $img['id'] ?>&pid=<?= $id ?>"
           onclick="return confirm('Hapus gambar ini?')">Hapus</a>
    </div>
<?php endforeach; ?>

</body>
</html>
