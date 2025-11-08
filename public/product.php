<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$st->execute([$id]);
$product = $st->fetch();

if (!$product) {
  http_response_code(404);
  exit('Product not found');
}

// Direct mapping between product name and image filename
$imageMap = [
  'Cold Pressed Groundnut Oil 1L' => '../assets/img/cold-pressed-groundnut-oil.jpeg',
  'Organic Basmati Rice 1kg' => '../assets/img/organic-basmati-rice.jpeg',
  'Sunrise Tea 250g' => '../assets/img/sunrise-tea.jpeg'
];

$imageFile = $imageMap[$product['name']] ?? '../assets/img/placeholder.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $qty = max(1, (int)($_POST['qty'] ?? 1));
  cart_add($product['id'], $qty);
  flash('success', 'Added to cart!');
  header('Location: cart.php');
  exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
  <div class="col-md-6 text-center">
    <img src="<?= $imageFile ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height:400px; object-fit:contain;">
  </div>
  <div class="col-md-6">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p class="text-muted fs-5"><?= money($product['price']) ?></p>
    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <form method="post" class="d-flex gap-2 align-items-center mt-3">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="number" name="qty" min="1" value="1" class="form-control w-auto">
      <button class="btn btn-primary">Add to Cart</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>