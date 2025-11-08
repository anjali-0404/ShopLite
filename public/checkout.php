<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php'; require_login();
cart_init();
$ids=array_keys($_SESSION['cart']??[]); if(!$ids){ header('Location: cart.php'); exit; }
$in=implode(',', array_fill(0,count($ids),'?')); $st=$pdo->prepare('SELECT * FROM products WHERE id IN ('.$in.')'); $st->execute($ids);
$rows=$st->fetchAll(); $idx=[]; foreach($rows as $r) $idx[$r['id']]=$r; $items=[]; $total=0.0;
foreach($_SESSION['cart'] as $pid=>$q){ if(isset($idx[$pid])){ $r=$idx[$pid]; $r['qty']=$q; $r['line_total']=$q*$r['price']; $items[]=$r; $total+=$r['line_total']; } }
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  $address=trim($_POST['address']??''); $city=trim($_POST['city']??''); $zip=trim($_POST['zip']??''); $payment=$_POST['payment']??'cod';
  if(!$address||!$city||!$zip) $errors[]='Complete address required';
  if(!$errors){
    $pdo->beginTransaction();
    try{
      $o=$pdo->prepare('INSERT INTO orders (user_id,total_amount,payment_method,status,address,city,zip) VALUES (?,?,?,?,?,?,?)');
      $o->execute([$_SESSION['user']['id'],$total,$payment,'PLACED',$address,$city,$zip]);
      $oid=(int)$pdo->lastInsertId();
      $oi=$pdo->prepare('INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)');
      foreach($items as $it){ $oi->execute([$oid,$it['id'],$it['qty'],$it['price']]); }
      $pdo->commit(); cart_clear(); header('Location: orders.php?placed=1'); exit;
    } catch(Throwable $t){ $pdo->rollBack(); $errors[]='Failed to place order'; }
  }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Checkout</h1>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<div class="row g-4">
  <div class="col-md-7">
    <form method="post" class="row g-3">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" required></textarea></div>
      <div class="col-md-6"><label class="form-label">City</label><input name="city" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">ZIP</label><input name="zip" class="form-control" required></div>
      <div class="col-12"><label class="form-label">Payment</label>
        <select name="payment" class="form-select"><option value="cod">Cash on Delivery</option><option value="dummy">Card/UPI (dummy)</option></select>
      </div>
      <div class="col-12"><button class="btn btn-primary">Place Order</button></div>
    </form>
  </div>
  <div class="col-md-5">
    <div class="card"><div class="card-header">Order Summary</div>
    <ul class="list-group list-group-flush">
      <?php foreach($items as $it): ?><li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($it['name']) ?> × <?= (int)$it['qty'] ?></span><span><?= money($it['line_total']) ?></span></li><?php endforeach; ?>
      <li class="list-group-item d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($total) ?></span></li>
    </ul></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
