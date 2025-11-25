<!DOCTYPE html>
<!-- public/register.php -->
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CampusMarket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-card">

            <div class="panel right-panel">
                <div class="hero">
                    <div class="hero-icons">
                        <div class="square large">🛍️</div>
                        <div class="square large">🎓</div>
                    </div>
                    <h3>Bergabunglah Bersama Kami!</h3>
                    <p class="hero-desc">
                        Daftar sekarang dan mulai jual beli produk dengan mahasiswa lainnya di Semarang.
                    </p>

                    <div class="benefits">

                        <div class="benefit">
                            <div class="b-icon">✨</div>
                            <div>
                                <strong>Gratis & Mudah</strong>
                                <div class="muted">Pendaftaran cepat tanpa biaya apapun</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🔒</div>
                            <div>
                                <strong>Aman & Terpercaya</strong>
                                <div class="muted">Data Anda dilindungi dengan enkripsi</div>
                            </div>
                        </div>

                        <div class="benefit">
                            <div class="b-icon">🚀</div>
                            <div>
                                <strong>Mulai Berjualan</strong>
                                <div class="muted">Langsung jualan setelah verifikasi akun</div>
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

                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step-dot active" data-step="1"></div>
                        <div class="step-dot" data-step="2"></div>
                        <div class="step-dot" data-step="3"></div>
                    </div>

                    <form action="register_process.php" method="POST" id="registerForm">

                        <!-- STEP 1 -->
                        <div class="form-step active" id="step1">
                            <h2 class="register-box">Register</h2>

                            <label for="name">Nama Lengkap</label>
                            <div class="input">
                                <span class="input-icon">👤</span>
                                <input type="text" id="name" name="name" placeholder="Masukkan nama" required>
                            </div>

                            <label for="username">Username</label>
                            <div class="input">
                                <span class="input-icon">@</span>
                                <input type="text" id="username" name="username" placeholder="username" required>
                            </div>
                            <br>
                            <br>


                            <button type="button" class="btn btn-primary next-btn" data-next="step2">Berikutnya</button>
                        </div>

                        <!-- STEP 2 -->
                        <div class="form-step" id="step2">
                            <h2 class="register-box">Register</h2>

                            <label for="email">Email</label>
                            <div class="input">
                                <span class="input-icon">✉️</span>
                                <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                            </div>

                            <label for="phone">Nomor Telepon</label>
                            <div class="input">
                                <span class="input-icon">📱</span>
                                <input type="tel" id="phone" name="phone" placeholder="" required>
                            </div>

                            <button type="button" class="btn btn-outline prev-btn" data-prev="step1">Kembali</button>
                            <button type="button" class="btn btn-primary next-btn" data-next="step3">Berikutnya</button>
                        </div>

                        <!-- STEP 3 -->
                        <div class="form-step" id="step3">
                            <h2 class="register-box">Register</h2>

                            <label for="password">Password</label>
                            <div class="input">
                                <span class="input-icon">🔒</span>
                                <input type="password" id="password" name="password"
                                    placeholder="Minimal 8 karakter" required>
                            </div>

                            <div class="input">
                                <span class="input-icon">🔒</span>
                                <input type="password" id="confirm_password" name="confirm_password"
                                    placeholder="Ulangi password" required>
                            </div>

                            <p id="password_error" class="error-message" style="display:none;color:red;font-size:13px;">
                                Password tidak cocok.
                            </p>


                            <input type="hidden" name="role" value="buyer">


                                <button type="button" class="btn btn-outline prev-btn" data-prev="step2">Kembali</button>
                                <button type="submit" class="btn btn-primary" id="submitRegister">Daftar Sekarang</button>
                        </div>

                    </form>

                    <div class="or"><span>atau</span></div>

                    <div class="register-box">
                        <p>Sudah punya akun?</p>
                        <a class="btn btn-outline" href="login.php">Login di sini</a>
                    </div>

                </div>
            </div>



        </div>
    </div>

    <script src="../assets/js/register.js"></script>

</body>

</html>