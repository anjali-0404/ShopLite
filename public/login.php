<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  $email=trim($_POST['email']??''); $pass=trim($_POST['password']??'');
  $st=$pdo->prepare('SELECT id,name,email,password FROM users WHERE email=?'); $st->execute([$email]); $u=$st->fetch();
  if(!$u || $pass !== $u['password']) $errors[]='Invalid credentials';
  else { $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email']]; header('Location: index.php'); exit; }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Login</h1>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<?php if($m=flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
  <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
  <div class="col-12"><button class="btn btn-primary">Login</button> <a class="btn btn-link" href="register.php">Create account</a></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
