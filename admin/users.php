<?php
require_once __DIR__ . '/../includes/config.php';
include "header.php";

$users = $pdo->query("SELECT * FROM accounts ORDER BY id DESC")->fetchAll();
?>

<link rel="stylesheet" href="../assets/css/admin_system.css">
<h2 style="margin: 20px 0;">Users Management</h2>

<table class="user-table">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>City</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td><?= htmlspecialchars($u['city']) ?></td>
        <td>
            <a class="action-btn btn-edit" href="edit_user.php?id=<?= $u['id'] ?>">Edit</a>
            <a class="action-btn btn-delete"
               href="delete_user.php?id=<?= $u['id'] ?>"
               onclick="return confirm('Delete user?')">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
