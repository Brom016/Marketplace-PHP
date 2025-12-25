<?php
// public/search.php

// 1. Include Config (Naik satu folder dari public ke root/includes)
require_once '../includes/config.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

// 2. Logika Pencarian
if ($keyword) {
    try {
        // Query mencari nama produk, deskripsi, atau kategori
        // Kita LEFT JOIN dengan product_images untuk mengambil gambar utama
        // Kita LEFT JOIN dengan categories untuk nama kategori
        $sql = "SELECT 
                    p.id, 
                    p.name, 
                    p.slug, 
                    p.price, 
                    p.stock,
                    c.name as category_name,
                    pi.url as image_url
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.status = 'active' 
                AND (
                    p.name LIKE :keyword 
                    OR p.description LIKE :keyword 
                    OR c.name LIKE :keyword
                )
                ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['keyword' => "%$keyword%"]);
        $results = $stmt->fetchAll();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian: <?php echo htmlspecialchars($keyword); ?> - MarketMM</title>
    <!-- Pastikan path CSS sesuai project Anda -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS Sederhana untuk Grid Produk (Bisa dipindah ke file style.css) */
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .search-header { margin-bottom: 30px; text-align: center; }
        
        /* Grid System */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .product-card:hover { transform: translateY(-5px); }
        
        .product-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            background-color: #eee;
        }
        
        .product-info { padding: 15px; }
        .cat-badge { font-size: 0.8rem; color: #888; text-transform: uppercase; }
        .product-name { margin: 5px 0; font-size: 1rem; font-weight: bold; color: #333; }
        .product-price { color: #e44d26; font-weight: bold; font-size: 1.1rem; }
        .no-result { text-align: center; color: #666; margin-top: 50px; }
        
        /* Style Search Box (Override dari include) */
        .cm-search input { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px; }
        .cm-search button { padding: 10px 15px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    
    <!-- 3. Memanggil Komponen Search Box (Agar user bisa cari ulang) -->
    <div class="search-header">
        <h2>Hasil Pencarian</h2>
        <?php include '../includes/components/search_box.php'; ?>
    </div>

    <!-- 4. Menampilkan Hasil -->
    <?php if ($keyword): ?>
        <p>Menampilkan hasil untuk: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong> (<?php echo count($results); ?> produk)</p>
        
        <?php if (count($results) > 0): ?>
            <div class="product-grid">
                <?php foreach ($results as $item): ?>
                    <?php 
                        // Mengatur path gambar (Asumsi gambar di upload folder)
                        // Sesuaikan path '../uploads/' jika struktur folder berbeda
                        $imgSrc = !empty($item['image_url']) 
                                  ? '../uploads/' . $item['image_url'] // Cek path uploads Anda
                                  : 'assets/images/no-image.png'; 
                    ?>
                    
                    <a href="product-detail.php?slug=<?php echo $item['slug']; ?>" class="product-card">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                        <div class="product-info">
                            <div class="cat-badge"><?php echo htmlspecialchars($item['category_name'] ?? 'Umum'); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="product-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-result">
                <i class="fas fa-box-open fa-3x"></i>
                <p>Produk tidak ditemukan. Coba kata kunci lain.</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p class="no-result">Silakan masukkan kata kunci pencarian.</p>
    <?php endif; ?>

</div>

</body>
</html>