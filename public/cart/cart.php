<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Cek login
$user = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Default profile picture
$profile_pic = "assets/img/default-profile.png";
if ($user && !empty($user['profile_picture'])) {
    $profile_pic = "../../uploads/profile/" . $user['profile_picture'];
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>CampusMarket — Cart</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>

<body>

    <?php include __DIR__ . "../../components/header.php"; ?>

    <center>
        <h1>Halaman Cart</h1>
    </center>

</body>

</html>