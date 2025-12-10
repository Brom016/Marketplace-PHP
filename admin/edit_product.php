<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid product ID.");


$categories = $pdo->query("SELECT * FROM categories")->fetchAll();


$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Product not found.");


$imgStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    
    $upd = $pdo->prepare("
        UPDATE products 
        SET name=?, description=?, price=?, stock=?, category_id=? 
        WHERE id=?
    ");
    $upd->execute([$name, $desc, $price, $stock, $category_id, $id]);

    
    if (!empty($_FILES['new_images']['name'][0])) {

        foreach ($_FILES['new_images']['name'] as $i => $filename) {

            if ($_FILES['new_images']['error'][$i] !== 0) continue;

            $tmp = $_FILES['new_images']['tmp_name'][$i];
            $cleanName = preg_replace("/[^a-zA-Z0-9.]/", "", $filename);

            
            $newName = "product_{$id}_" . uniqid() . "_" . $i . "." . pathinfo($cleanName, PATHINFO_EXTENSION);
            $path = __DIR__ . "/../uploads/products/" . $newName;

            move_uploaded_file($tmp, $path);

            
            $pdo->prepare("
                INSERT INTO product_images (product_id, url, is_primary)
                VALUES (?, ?, 0)
            ")->execute([$id, $newName]);
        }
    }

    header("Location: edit_product.php?id=" . $id);
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>

    <link rel="stylesheet" href="../assets/css/edit_product.css">
</head>

<body>

<h2>Edit Product</h2>

<form method="post" enctype="multipart/form-data">

    <label>Product Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">

    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Price:</label>
    <input type="number" name="price" value="<?= $product['price'] ?>">

    <label>Stock:</label>
    <input type="number" name="stock" value="<?= $product['stock'] ?>">

    <label>Category:</label>
    <select name="category_id">
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Current Images:</label>
    <div class="image-wrapper">
        <?php foreach ($images as $img): ?>
            <div class="image-box">
                <img class="prod-thumb" src="../uploads/products/<?= $img['url'] ?>">

                <a class="delete-btn" 
                   href="delete_image.php?id=<?= $img['id'] ?>&pid=<?= $id ?>"
                   onclick="return confirm('Delete this image?')">
                    Delete
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <label>Add New Images:</label>
    <input type="file" name="new_images[]" multiple>

    <button type="submit">Save Changes</button>

</form>

</body>
</html>
