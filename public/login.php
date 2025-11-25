<?php
// public/login.php
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$registered = isset($_GET['registered']);
$err = isset($_GET['err']) ? $_GET['err'] : null;
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Mahasiswa-Market — Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-card">

            <!-- RIGHT PANEL-->
            <div class="panel right-panel">
                <div class="hero">
                    <div class="hero-icons">
                        <div class="square large">🛍️</div>
                        <div class="square large">🎓</div>
                    </div>
                    <h3>Selamat Datang!</h3>
                    <p class="hero-desc">
                        Platform marketplace khusus untuk mahasiswa Semarang.
                        Jual beli produk dan jasa karya teman kampusmu!
                    </p>

                    <div class="benefits">

                        <div class="benefit">
                            <div class="b-icon">👥</div>
                            <div>
                                <strong>Komunitas Mahasiswa</strong>
                                <div class="muted">Terhubung dengan ribuan mahasiswa dari berbagai kampus</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">📈</div>
                            <div>
                                <strong>Kembangkan Bisnismu</strong>
                                <div class="muted">Mulai berjualan dan kembangkan skill entrepreneurship</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">⭐</div>
                            <div>
                                <strong>Produk Berkualitas</strong>
                                <div class="muted">Temukan produk dan jasa terbaik dari mahasiswa berbakat</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- LEFT PANEL / Login -->
            <div class="panel left-panel">
                <div class="brand">
                    <div class="brand-icon">
                        <div class="brand-img   ">
                            <img src="../assets/images/icontrs.png" alt="icon">
                        </div>
                    </div>
                    <h3>Marketplace Produk Mahasiswa</h3>
                    <div class="brand-underline"></div>
                </div>

                <div class="login-box">
                    <h2 class="register-box">Login</h2>

                    <?php if ($registered): ?>
                        <p style="color: var(--green-1); text-align: center; margin: 10px 0; font-size: 14px;">
                            ✓ Registrasi berhasil, silakan login.
                        </p>
                    <?php endif; ?>

                    <?php if ($err): ?>
                        <p style="color: #ef4444; text-align: center; margin: 10px 0; font-size: 14px; background: #fee; padding: 10px; border-radius: 8px;">
                            ⚠️ <?php echo htmlspecialchars($err); ?>
                        </p>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST" autocomplete="off">

                        <label>Username atau Email</label>
                        <div class="input">
                            <span class="input-icon">👤</span>
                            <input name="identity" type="text" placeholder="username atau email" required>
                        </div>

                        <label>Password</label>
                        <div class="input">
                            <span class="input-icon">🔒</span>
                            <input name="password" type="password" placeholder="Masukkan password Anda" required>
                        </div>

                        <br>
                        <div style="text-align: right;">
                            <a href="forgot_password.php" style="color: var(--green-1); text-decoration: none; font-size: 14px;">Lupa password?</a>
                        </div>

                        <br>

                        <button class="btn btn-primary" type="submit">Login</button>

                        <div class="or"><span>atau</span></div>

                        <div class="register-box">
                            <p style="margin-bottom: 8px;">Belum punya akun?</p>
                            <a class="btn btn-outline" href="register.php">Daftar Sekarang</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>