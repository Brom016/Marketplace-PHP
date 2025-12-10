<?php
require_once __DIR__ . '/../includes/config.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die("User not found");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $city = $_POST['city'];

    $stmt = $pdo->prepare("UPDATE accounts SET name=?, role=?, city=? WHERE id=?");
    $stmt->execute([$name, $role, $city, $id]);

    header("Location: users.php");
    exit;
}
?>
<h2>Edit User</h2>

<form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"><br><br>
    Role: 
    <select name="role">
        <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
        <option value="seller" <?= $user['role']=='seller'?'selected':'' ?>>Seller</option>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
    </select>
    <br><br>
    City: <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>"><br><br>

    <button type="submit">Save</button>
</form>
