<?php
$config = require __DIR__ . '/../config/config.php';
$dsn = 'mysql:host=' . $config['DB_HOST'] . ';dbname=' . $config['DB_NAME'] . ';charset=utf8mb4';
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
try {
  $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options);
} catch (PDOException $e) {
  if ($config['APP_DEBUG']) { die('DB connection failed: ' . $e->getMessage()); }
  http_response_code(500); exit('Internal Server Error');
}
