<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function base_url(string $path=''): string {
  $config = require __DIR__ . '/../config/config.php';
  return rtrim($config['APP_URL'], '/') . '/' . ltrim($path, '/');
}
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_verify(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
      http_response_code(419); exit('CSRF token mismatch');
    }
  }
}
function money($amount): string { return '₹' . number_format((float)$amount, 2); }
function flash(string $key, ?string $msg=null) {
  if ($msg === null) { $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v; }
  $_SESSION['flash'][$key] = $msg;
}
function is_logged_in(): bool { return !empty($_SESSION['user']); }
function require_login(): void { if (!is_logged_in()) { header('Location: ' . base_url('public/login.php')); exit; } }
function is_admin(): bool { return !empty($_SESSION['admin']); }
function require_admin(): void { if (!is_admin()) { header('Location: ' . base_url('admin/login.php')); exit; } }

# Cart helpers
function cart_init(): void { if (!isset($_SESSION['cart'])) $_SESSION['cart'] = []; }
function cart_add(int $product_id, int $qty=1): void {
  cart_init();
  $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + max(1, $qty);
}
function cart_set(int $product_id, int $qty): void {
  cart_init();
  if ($qty <= 0) unset($_SESSION['cart'][$product_id]);
  else $_SESSION['cart'][$product_id] = $qty;
}
function cart_remove(int $product_id): void { cart_init(); unset($_SESSION['cart'][$product_id]); }
function cart_clear(): void { $_SESSION['cart'] = []; }
