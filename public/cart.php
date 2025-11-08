<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php';
cart_init();
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  if(isset($_POST['update'])){ foreach($_POST['qty'] as $pid=>$qty) cart_set((int)$pid,(int)$qty); }
  elseif(isset($_POST['remove'])){ cart_remove((int)$_POST['remove']); }
  elseif(isset($_POST['clear'])){ cart_clear(); }
  header('Location: cart.php'); exit;
}
$ids=array_keys($_SESSION['cart']); $items=[]; $total=0.0;
if($ids){
  $in=implode(',', array_fill(0,count($ids),'?')); $st=$pdo->prepare('SELECT * FROM products WHERE id IN ('.$in.')'); $st->execute($ids);
  $rows=$st->fetchAll(); $idx=[]; foreach($rows as $r) $idx[$r['id']]=$r;
  foreach($_SESSION['cart'] as $pid=>$qty){ if(isset($idx[$pid])){ $r=$idx[$pid]; $r['qty']=$qty; $r['line_total']=$qty*$r['price']; $items[]=$r; $total+=$r['line_total']; } }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Cart</h1>
<?php if($m=flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<table class="table align-middle">
  <thead><tr><th>Product</th><th>Price</th><th width="120">Qty</th><th>Total</th><th></th></tr></thead>
  <tbody>
  <?php foreach($items as $it): ?>
  <tr>
    <td><?= htmlspecialchars($it['name']) ?></td>
    <td><?= money($it['price']) ?></td>
    <td><input type="number" class="form-control" name="qty[<?= (int)$it['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="1"></td>
    <td><?= money($it['line_total']) ?></td>
    <td><button name="remove" value="<?= (int)$it['id'] ?>" class="btn btn-sm btn-outline-danger">Remove</button></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<div class="d-flex justify-content-between">
  <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
  <div>
    <button name="update" class="btn btn-outline-primary">Update</button>
    <button name="clear" class="btn btn-outline-warning">Clear</button>
    <a href="checkout.php" class="btn btn-primary<?= $total<=0?' disabled':'' ?>">Checkout (<?= money($total) ?>)</a>
  </div>
</div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
