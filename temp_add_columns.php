<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);
$pdo->exec('ALTER TABLE users ADD COLUMN IF NOT EXISTS zone_id INTEGER');
$pdo->exec('ALTER TABLE users ADD COLUMN IF NOT EXISTS school_id INTEGER');
echo 'Columns added';
