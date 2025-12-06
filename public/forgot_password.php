<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$err = isset($_GET['err']) ? $_GET['err'] : null;
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Mahasiswa-Market — Lupa Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-card">

            <!-- RIGHT PANEL -->
            <div class="panel right-panel">
                <div class="hero">
                    <div class="hero-icons">
                        <div class="square large">🔑</div>
                        <div class="square large">📩</div>
                    </div>

                    <h3>Lupa Password?</h3>
                    <p class="hero-desc">
                        Tenang, masih bisa diselamatkan.
                        Masukkan email kamu dan kami akan kirim OTP untuk reset password.
                    </p>

                    <div class="benefits">

                        <div class="benefit">
                            <div class="b-icon">📧</div>
                            <div>
                                <strong>Kirim ke Email</strong>
                                <div class="muted">OTP dikirim langsung ke inbox kamu</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">⚡</div>
                            <div>
                                <strong>Proses Cepat</strong>
                                <div class="muted">Cuma butuh kurang dari 1 menit</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🔒</div>
                            <div>
                                <strong>100% Aman</strong>
                                <div class="muted">Kami tidak menyimpan password lama kamu</div>
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
                    <p class="muted" style="text-align: center;">
                        Masukkan email untuk menerima kode OTP (6 digit)
                    </p>

                    <?php if ($err): ?>
                        <p style="color:#ef4444;text-align:center;margin:10px 0;font-size:14px;background:#fee;padding:10px;border-radius:8px;">
                            ⚠️ <?php echo htmlspecialchars($err); ?>
                        </p>
                    <?php endif; ?>

                    <form action="send_otp.php" method="POST" autocomplete="off">
                        
                        <label>Email</label>
                        <div class="input">
                            <span class="input-icon">📧</span>
                            <input 
                                type="email" 
                                name="email" 
                                placeholder="Masukkan email kamu"
                                required>
                        </div>

                        <br>

                        <button class="btn btn-primary" type="submit">
                            Kirim Kode OTP
                        </button>

                        <div class="or"><span>atau</span></div>

                        <div class="register-box">
                            <p style="margin-bottom:8px;">Sudah ingat password?</p>
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
