<?php

require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: forgot_password.php');
    exit;
}

$err = isset($_GET['err']) ? $_GET['err'] : null;
$success = isset($_GET['success']);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Mahasiswa-Market — Reset Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-card">

            <!-- RIGHT PANEL -->
            <div class="panel right-panel">
                <div class="hero">
                    <div class="hero-icons">
                        <div class="square large">🔐</div>
                        <div class="square large">✅</div>
                    </div>

                    <h3>Buat Password Baru</h3>
                    <p class="hero-desc">
                        Ini langkah terakhir.
                        Buat password baru yang kuat dan jangan pakai yang gampang ditebak.
                    </p>

                    <div class="benefits">

                        <div class="benefit">
                            <div class="b-icon">💡</div>
                            <div>
                                <strong>Gunakan Password Kuat</strong>
                                <div class="muted">Gabungkan huruf, angka & simbol</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🔑</div>
                            <div>
                                <strong>Minimal 8 Karakter</strong>
                                <div class="muted">Semakin panjang, semakin aman</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🚫</div>
                            <div>
                                <strong>Jangan Pakai yang Lama</strong>
                                <div class="muted">Hindari pengulangan password</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- LEFT PANEL -->
            <div class="panel left-panel">

                <div class="brand">
                    <div class="brand-icon">
                        <div class="brand-img">
                            <img src="../assets/images/icontrs.png" alt="icon">
                        </div>
                    </div>
                    <h3>Marketplace Produk Mahasiswa</h3>
                    <div class="brand-underline"></div>
                </div>

                <div class="login-box">
                    <h2 class="register-box">Reset Password</h2>
                    <p class="muted" style="text-align:center">
                        Masukkan password baru untuk akun kamu
                    </p>

                    <?php if ($success): ?>
                        <p style="color: var(--green-1); text-align: center; margin: 10px 0; font-size: 14px;">
                            ✅ Password berhasil diubah. Silakan login kembali.
                        </p>
                    <?php endif; ?>

                    <?php if ($err): ?>
                        <p style="color:#ef4444;text-align:center;margin:10px 0;font-size:14px;background:#fee;padding:10px;border-radius:8px;">
                            ⚠️ <?php echo htmlspecialchars($err); ?>
                        </p>
                    <?php endif; ?>

                    <form action="reset_password_process.php" method="POST" autocomplete="off">

                        <label>Password Baru</label>
                        <div class="input">
                            <span class="input-icon">🔒</span>
                            <input
                                type="password"
                                name="password"
                                placeholder="Masukkan password baru"
                                minlength="8"
                                required>
                        </div>

                        <label>Konfirmasi Password</label>
                        <div class="input">
                            <span class="input-icon">✅</span>
                            <input
                                type="password"
                                name="password_confirm"
                                placeholder="Ulangi password baru"
                                minlength="8"
                                required>
                        </div>

                        <br>

                        <button class="btn btn-primary" type="submit">
                            Reset Password Sekarang
                        </button>

                        <div class="or"><span>atau</span></div>

                        <div class="register-box">
                            <p style="margin-bottom:8px;">Batal reset?</p>
                            <a class="btn btn-outline" href="login.php">
                                Kembali ke Login
                            </a>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</body>
</html>
