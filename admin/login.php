<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php';
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  $email=trim($_POST['email']??''); $pass=trim($_POST['password']??'');
  $st=$pdo->prepare('SELECT id,email,password FROM admin_users WHERE email=?'); $st->execute([$email]); $a=$st->fetch();
  if(!$a || $pass !== $a['password']) $errors[]='Invalid credentials';
  else { $_SESSION['admin']=['id'=>$a['id'],'email'=>$a['email']]; header('Location: index.php'); exit; }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Admin Login</h1>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
  <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
  <div class="col-12"><button class="btn btn-primary">Login</button></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
