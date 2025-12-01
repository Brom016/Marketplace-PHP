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

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat   = $_POST['category_id'];
    $desc  = $_POST['description'];

    $slug = strtolower(str_replace(" ", "-", $name)) . "-" . time();

    // insert product
    $stmt = $pdo->prepare("
        INSERT INTO products (seller_id, category_id, name, slug, price, stock, description)
        VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([$seller_id, $cat, $name, $slug, $price, $stock, $desc]);

    $product_id = $pdo->lastInsertId();

    // UPLOAD GAMBAR
    if (!empty($_FILES['images']['name'][0])) {

        if (count($_FILES['images']['name']) > 10) {
            die("Max 10 gambar!");
        }

        $uploadDir = __DIR__ . "/../../uploads/products/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['images']['name'] as $i => $imgName) {

            $tmp = $_FILES['images']['tmp_name'][$i];
            $ext = pathinfo($imgName, PATHINFO_EXTENSION);

            $unique = uniqid();
            $newName = "product_{$product_id}_{$unique}_{$i}.$ext";

            move_uploaded_file($tmp, $uploadDir . $newName);

            // SAVE CORRECT COLUMNS
            $stmt = $pdo->prepare("
                INSERT INTO product_images (product_id, url, is_primary)
                VALUES (?,?,?)
            ");
            $stmt->execute([$product_id, $newName, ($i == 0 ? 1 : 0)]);
        }
    }

    header("Location: products.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head><title>Tambah Produk</title></head>
<body>

<h2>Tambah Produk</h2>

<form method="post" enctype="multipart/form-data">

    Nama Produk: <br>
    <input type="text" name="name" required><br><br>

    Kategori: <br>
    <select name="category_id">
        <?php foreach ($cats as $c): ?>
        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    Harga: <br>
    <input type="number" name="price" required><br><br>

    Stok: <br>
    <input type="number" name="stock" required><br><br>

    Deskripsi:<br>
    <textarea name="description"></textarea><br><br>

    Gambar Produk (max 10):<br>
    <input type="file" name="images[]" multiple accept="image/*"><br><br>

    <button type="submit">Simpan</button>

</form>


</body>
</html>
