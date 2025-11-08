<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php'; require_admin();
$orders=$pdo->query('SELECT o.*, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h1>Orders</h1>
<table class="table">
  <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Payment</th><th>Address</th></tr></thead>
  <tbody>
  <?php foreach($orders as $o): ?>
    <tr>
      <td><?= (int)$o['id'] ?></td>
      <td><?= htmlspecialchars($o['email']) ?></td>
      <td><?= money($o['total_amount']) ?></td>
      <td><?= htmlspecialchars($o['status']) ?></td>
      <td><?= htmlspecialchars($o['payment_method']) ?></td>
      <td><?= htmlspecialchars($o['address'] . ', ' . $o['city'] . ' ' . $o['zip']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
