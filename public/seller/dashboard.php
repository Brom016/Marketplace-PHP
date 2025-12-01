<?php
require_once __DIR__ . "/../../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan!");
}

if ($user['role'] !== 'seller') {
    die("Akses ditolak!");
}

// Ambil kategori dari database
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CampusMarket — Dashboard Seller</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body>

<!-- HEADER -->
<?php include __DIR__ . "/../components/header.php"; ?>

<div class="layout">

    <!-- SIDEBAR KATEGORI -->
    <div class="sidebar">
        <h3>Kategori</h3>

        <ul>
            <li><a href="#">Semua Kategori</a></li>

            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $c): ?>
                    <li>
                        <a href="#">
                            <?= htmlspecialchars($c['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><em>Tidak ada kategori</em></li>
            <?php endif; ?>
        </ul>
    </div>

    <center>
        <br>
        <a href="products.php">Kelola Produk</a>
    </center>

</div>

</body>
</html>
