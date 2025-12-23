<?php

$stmt = $pdo->query("
    SELECT id, name 
    FROM categories 
    ORDER BY name ASC 
    LIMIT 6
");

$footer_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../../assets/css/footer.css">

<footer>

  <div class="container">   <!-- Membatasi lebar kntn -->
    <div class="footer-content">   <!-- Wrapper utama untuk footer. -->


      <div class="footer-section"> <!-- Satu blok/kolom footer -->
        <h5>Campus Market</h5>
        <p>
          Marketplace khusus mahasiswa yang menyediakan berbagai produk kreatif,
          kebutuhan kuliah, dan layanan jasa dari mahasiswa untuk mahasiswa.
        </p>
      </div>

      <!-- Kategori -->
      <div class="footer-section">
        <h5>Categories</h5>

        <?php if ($footer_categories): ?>
          <?php foreach ($footer_categories as $cat): ?>
            <a href="/marketmm/public/index.php?category=<?= $cat['id'] ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Tidak ada kategori</p>
        <?php endif; ?>
      </div>

      <!-- Informasi -->
      <div class="footer-section">
        <h5>Informations</h5>
        <a href="/marketmm/public/profile/profile.php">My Account</a>
        <a href="/marketmm/public/seller/register_seller.php">Sell on Campus Market</a>
      </div>

      <!-- Kontak -->
      <div class="footer-section">
        <h5>Contact</h5>
        <div class="contact-item">
          <i class="fas fa-home"></i>
          <span>Semarang, Jawa Tengah</span>
        </div>
        <div class="contact-item">
          <i class="fas fa-envelope"></i>
          <span>marketplacemahasiswa@gmail.com</span>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <div class="copyright">
        © <?= date('Y') ?> Campus Market. Developed by <strong>Alang-Alang</strong>
      </div>
    </div>
  </div>
</footer>