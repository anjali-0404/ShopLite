<?php
return [
  'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
  'DB_NAME' => getenv('DB_NAME') ?: 'shoplite_db',
  'DB_USER' => getenv('DB_USER') ?: 'root',
  'DB_PASS' => getenv('DB_PASS') ?: '',
  'APP_DEBUG' => getenv('APP_DEBUG') !== false ? (bool)filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) : true,
  'APP_URL' => getenv('APP_URL') ?: 'http://localhost/Ecommerce'
];
