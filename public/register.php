<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CampusMarket — Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-card">

            <!-- RIGHT PANEL -->
            <div class="panel right-panel">
                <div class="hero">
                    <div class="hero-icons">
                        <div class="square large">🚀</div>
                        <div class="square large">✨</div>
                    </div>
                    <h3>Bergabung Sekarang!</h3>
                    <p class="hero-desc">
                        Mulai perjalanan entrepreneur-mu bersama ribuan mahasiswa Semarang.
                        Daftar gratis dan mulai berjualan hari ini!
                    </p>

                    <div class="benefits">

                        <div class="benefit">
                            <div class="b-icon">🎯</div>
                            <div>
                                <strong>Gratis & Mudah</strong>
                                <div class="muted">Registrasi hanya 2 menit, langsung bisa jualan</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">💰</div>
                            <div>
                                <strong>Tanpa Biaya Admin</strong>
                                <div class="muted">Jual produkmu tanpa potongan biaya transaksi</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🔒</div>
                            <div>
                                <strong>Aman & Terpercaya</strong>
                                <div class="muted">Data terenkripsi dan sistem pembayaran aman</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- LEFT PANEL / Register Form -->
            <div class="panel left-panel">
                <div class="brand">
                    <div class="brand-icon">🎓</div>
                    <h3>Marketplace Produk Mahasiswa</h3>
                    <div class="brand-underline"></div>
                </div>

                <div class="login-box">
                    <h2 class="register-box">Daftar Akun Baru</h2>
                    <p class="muted" style="text-align:center;">Lengkapi data diri untuk memulai</p>

                    <form action="register_process.php" method="POST" autocomplete="off">

                        <label>Nama Lengkap</label>
                        <div class="input">
                            <span class="input-icon">👤</span>
                            <input name="name" type="text" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <label>Username</label>
                        <div class="input">
                            <span class="input-icon">@</span>
                            <input name="username" type="text" placeholder="Pilih username unik" required>
                        </div>

                        <label>Email</label>
                        <div class="input">
                            <span class="input-icon">✉️</span>
                            <input name="email" type="email" placeholder="email@mahasiswa.ac.id" required>
                        </div>

                        <label>Nomor Telepon</label>
                        <div class="input">
                            <span class="input-icon">📱</span>
                            <input name="phone" type="tel" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <label>Password</label>
                        <div class="input">
                            <span class="input-icon">🔒</span>
                            <input name="password" type="password" placeholder="Minimal 6 karakter" required>
                        </div>

                        <label>Daftar Sebagai</label>
                        <div class="input">
                            <span class="input-icon">🏷️</span>
                            <select name="role" style="border:0; outline:none; background:transparent; width:100%; font-size:15px; color:#0f172a; cursor:pointer;">
                                <option value="buyer">Buyer (Pembeli)</option>
                                <option value="seller">Seller (Penjual)</option>
                            </select>
                        </div>

                        <br>

                        <button class="btn btn-primary" type="submit">Daftar Sekarang</button>

                        <div class="or"><span>atau</span></div>

                        <div class="register-box">
                            <p style="margin-bottom: 8px;">Sudah punya akun?</p>
                            <a class="btn btn-outline" href="login.php">Login di Sini</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>