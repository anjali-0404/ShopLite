<?php
require __DIR__ . '/../includes/db.php'; require __DIR__ . '/../includes/functions.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_verify();
  $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $pass=trim($_POST['password']??'');
  if(!$name) $errors[]='Name is required';
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Valid email required';
  if(strlen($pass)<4) $errors[]='Password must be at least 4 chars';
  if(!$errors){
    $st=$pdo->prepare('SELECT id FROM users WHERE email=?'); $st->execute([$email]);
    if($st->fetch()) $errors[]='Email already registered';
    else { $st=$pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)'); $st->execute([$name,$email,$pass]); flash('success','Account created. Please login.'); header('Location: login.php'); exit; }
  }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Create account</h1>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
  <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
  <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
  <div class="col-12"><button class="btn btn-primary">Register</button> <a class="btn btn-link" href="login.php">Already have an account?</a></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
