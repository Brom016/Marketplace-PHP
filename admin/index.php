<?php
require_once __DIR__ . '/../includes/config.php';
include "header.php";

$total_users = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_stock = $pdo->query("SELECT SUM(stock) FROM products")->fetchColumn();
$total_sold = $pdo->query("SELECT COUNT(*) FROM cod_transactions WHERE status = 'completed'")->fetchColumn();
?>

<link rel="stylesheet" href="../assets/css/admin.css">

<div class="admin-layout">

    <div class="admin-stats">
        <div class="admin-card"><h3>Total Users</h3><h1><?= $total_users ?></h1></div>
        <div class="admin-card"><h3>Total Products</h3><h1><?= $total_products ?></h1></div>
       
        <div class="admin-card"><h3>Sold</h3><h1><?= $total_sold ?></h1></div>
    </div>

    <div class="admin-chart-box">
        <div class="section-title">📊 Grafik Statistik</div>
        <canvas id="statsChart"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/admin_chart.js"></script>

<script>
const statsData = {
    users: <?= $total_users ?>,
    products: <?= $total_products ?>,
    stock: <?= $total_stock ?>,
    sold: <?= $total_sold ?>
};
</script>
