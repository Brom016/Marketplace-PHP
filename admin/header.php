<link rel="stylesheet" href="../assets/css/header.css">

<div class="cm-header admin-header">
    <div class="cm-container admin-container">

       
       <!-- LOGO -->
        <?php
        $iconA = "../assets/images/icontrs.png";
        $iconB = "../../assets/images/icontrs.png";

        $logo = file_exists($iconA) ? $iconA : $iconB;
        ?>
        <a href="../index.php" class="cm-logo">
            <img src="<?= $logo ?>" alt="Logo">
            <span>Admin Panel</span>
        </a>

        <!-- NAV MENU -->
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="users.php">Users</a>
            <a href="products.php">Products</a>
            <a href="transactions.php">COD Transactions</a>
        </nav>
        

        <!-- LOGOUT -->
        <a href="../public/logout.php" class="admin-logout">Logout</a>
    </div>
</div>
