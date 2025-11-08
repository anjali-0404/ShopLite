<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php'; require_login();
$st=$pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC'); $st->execute([$_SESSION['user']['id']]); $orders=$st->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h1>My Orders</h1>
<?php if(isset($_GET['placed'])): ?><div class="alert alert-success">Order placed successfully!</div><?php endif; ?>
<div class="accordion" id="acc">
<?php foreach($orders as $o): $it=$pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE order_id=?'); $it->execute([$o['id']]); $items=$it->fetchAll(); ?>
  <div class="accordion-item">
    <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#c<?= (int)$o['id'] ?>">Order #<?= (int)$o['id'] ?> — <?= htmlspecialchars($o['status']) ?> — <?= money($o['total_amount']) ?></button></h2>
    <div id="c<?= (int)$o['id'] ?>" class="accordion-collapse collapse"><div class="accordion-body">
      <div class="mb-2 text-muted"><?= htmlspecialchars($o['address'] . ', ' . $o['city'] . ' ' . $o['zip']) ?></div>
      <ul><?php foreach($items as $it): ?><li><?= htmlspecialchars($it['name']) ?> × <?= (int)$it['quantity'] ?> — <?= money($it['price']*$it['quantity']) ?></li><?php endforeach; ?></ul>
    </div></div>
  </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
