<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  if(isset($_POST['create'])){
    $name=trim($_POST['name']); $price=(float)$_POST['price']; $desc=trim($_POST['description']);
    $st=$pdo->prepare('INSERT INTO products (name,description,price) VALUES (?,?,?)'); $st->execute([$name,$desc,$price]);
    flash('success','Product created'); header('Location: products.php'); exit;
  } elseif(isset($_POST['update'])){
    $id=(int)$_POST['id']; $name=trim($_POST['name']); $price=(float)$_POST['price']; $desc=trim($_POST['description']);
    $st=$pdo->prepare('UPDATE products SET name=?, description=?, price=? WHERE id=?'); $st->execute([$name,$desc,$price,$id]);
    flash('success','Product updated'); header('Location: products.php'); exit;
  } elseif(isset($_POST['delete'])){
    $id=(int)$_POST['delete']; $st=$pdo->prepare('DELETE FROM products WHERE id=?'); $st->execute([$id]);
    flash('success','Product deleted'); header('Location: products.php'); exit;
  }
}
$products=$pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h1>Products</h1>
<?php if($m=flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<div class="row">
  <div class="col-md-6">
    <h5>Create product</h5>
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="col-12"><input class="form-control" name="name" placeholder="Name" required></div>
      <div class="col-12"><textarea class="form-control" name="description" placeholder="Description"></textarea></div>
      <div class="col-6"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price" required></div>
      <div class="col-12"><button class="btn btn-primary" name="create">Create</button></div>
    </form>
  </div>
  <div class="col-md-6">
    <h5>Existing</h5>
    <?php foreach($products as $p): ?>
      <form method="post" class="border rounded p-2 mb-2">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <div class="mb-1"><input class="form-control" name="name" value="<?= htmlspecialchars($p['name']) ?>"></div>
        <div class="mb-1"><textarea class="form-control" name="description"><?= htmlspecialchars($p['description']) ?></textarea></div>
        <div class="mb-2"><input class="form-control" name="price" type="number" step="0.01" value="<?= htmlspecialchars($p['price']) ?>"></div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-primary" name="update">Update</button>
          <button class="btn btn-sm btn-outline-danger" name="delete" value="<?= (int)$p['id'] ?>">Delete</button>
        </div>
      </form>
    <?php endforeach; ?>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
