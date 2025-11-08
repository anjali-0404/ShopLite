<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php'; require_admin();
include __DIR__ . '/../includes/header.php';
?>
<h1>Admin Dashboard</h1>
<ul>
  <li><a href="products.php">Manage Products</a></li>
  <li><a href="orders.php">View Orders</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
<?php include __DIR__ . '/../includes/footer.php'; ?>
