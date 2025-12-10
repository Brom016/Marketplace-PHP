<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) die("Invalid product ID.");

// Ambil kategori
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Ambil produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("Product not found.");

// Ambil gambar
$imgStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    // Update produk
    $upd = $pdo->prepare("
        UPDATE products 
        SET name=?, description=?, price=?, stock=?, category_id=? 
        WHERE id=?
    ");
    $upd->execute([$name, $desc, $price, $stock, $category_id, $id]);

    // Upload gambar baru jika ada
    if (!empty($_FILES['images']['name'][0])) {

        // hapus gambar lama
        $pdo->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$id]);

        foreach ($_FILES['images']['name'] as $i => $filename) {
            $tmp = $_FILES['images']['tmp_name'][$i];
            $newName = time() . "_$filename";
            $path = __DIR__ . "/../uploads/products/" . $newName;

            move_uploaded_file($tmp, $path);

            $pdo->prepare("
                INSERT INTO product_images (product_id, image_url) 
                VALUES (?, ?)
            ")->execute([$id, $newName]);
        }
    }

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
</head>
<body>

<h2>Edit Product</h2>

<form method="post" enctype="multipart/form-data">

    <label>Product Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <label>Price:</label><br>
    <input type="number" name="price" value="<?= $product['price'] ?>"><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?= $product['stock'] ?>"><br><br>

    <label>Category:</label><br>
    <select name="category_id">
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Current Images:</label><br>
    <?php foreach ($images as $img): ?>
        <img src="../uploads/products/<?= $img['image_url'] ?>" width="80" style="margin-right:10px;">
    <?php endforeach; ?>
    <br><br>

    <label>Upload New Images (replace all):</label><br>
    <input type="file" name="images[]" multiple><br><br>

    <button type="submit">Save Changes</button>

</form>

</body>
</html>
