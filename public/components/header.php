<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../../assets/css/header.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="cm-header">
    <div class="cm-container">

        <!-- LOGO -->
        <?php
        $iconA = "../assets/images/icontrs.png";
        $iconB = "../../assets/images/icontrs.png";

        $logo = file_exists($iconA) ? $iconA : $iconB;
        ?>
        <a href="../index.php" class="cm-logo">
            <img src="<?= $logo ?>" alt="Logo">
            <span>CampusMarket</span>
        </a>

        <!-- SEARCH -->
        <div class="cm-search">
            <form>
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari produk mahasiswa...">
            </form>
        </div>

        <!-- RIGHT MENU -->
        <div class="cm-menu-right">

            <a href="cart/cart.php" class="cm-icon-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cm-badge green">2</span>
            </a>

            <a href="chat/chat.php" class="cm-icon-btn">
                <i class="fa-solid fa-message"></i>
                <span class="cm-badge">3</span>
            </a>

            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $is_profile_page =  ($current_page === 'profile.php');
            ?>

            <?php if (!$user): ?>
                <div class="cm-auth">
                    <a href="login.php">Sign In</a> |
                    <a href="register.php">Sign Up</a>
                </div>

            <?php else: ?>
                <div
                    class="cm-profile"
                    <?php if (!$is_profile_page): ?>
                    onclick="window.location='profile/profile.php'"
                    style="cursor:pointer;"
                    <?php endif; ?>>
                    <img src="<?= htmlspecialchars($profile_pic) ?>" class="cm-profile-img">
                    <div class="cm-user-info">
                        <span><?= htmlspecialchars($user['name']) ?></span>
                        <small>Mahasiswa</small>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>