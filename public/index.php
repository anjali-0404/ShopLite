<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

$search = trim($_GET['q'] ?? '');
if ($search) {
  $st = $pdo->prepare('SELECT * FROM products WHERE name LIKE :q OR description LIKE :q ORDER BY id DESC');
  $st->execute([':q' => '%' . $search . '%']);
  $products = $st->fetchAll();
} else {
  $products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
}

include __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Products</h1>
<form class="row g-3 mb-4" method="get">
  <div class="col-auto flex-grow-1">
    <input class="form-control" name="q" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Search</button>
  </div>
</form>

<div class="row row-cols-1 row-cols-md-3 g-4">
  <?php foreach ($products as $p):
    // Direct image mapping
    $imageMap = [
      'Cold Pressed Groundnut Oil 1L' => '../assets/img/cold-pressed-groundnut-oil.jpeg',
      'Organic Basmati Rice 1kg' => '../assets/img/organic-basmati-rice.jpeg',
      'Sunrise Tea 250g' => '../assets/img/sunrise-tea.jpeg'
    ];

    $imgFile = $imageMap[$p['name']] ?? '../assets/img/placeholder.png';
    $productUrl = 'product.php?id=' . (int)$p['id'];
  ?>
    <div class="col">
      <div class="card h-100 shadow-sm">
        <a href="<?= $productUrl ?>" class="text-decoration-none">
          <img src="<?= $imgFile ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
        </a>
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">
            <a href="<?= $productUrl ?>" class="text-dark text-decoration-none">
              <?= htmlspecialchars($p['name']) ?>
            </a>
          </h5>
          <p class="card-text small text-muted"><?= htmlspecialchars(mb_strimwidth($p['description'] ?? '', 0, 120, '…')) ?></p>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="fw-bold"><?= money($p['price']) ?></span>
            <a href="<?= $productUrl ?>" class="btn btn-sm btn-outline-primary">View</a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>