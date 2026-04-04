<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);
foreach ($pdo->query('SELECT * FROM migration ORDER BY apply_time DESC LIMIT 5') as $r) echo $r['version'] . ' - ' . $r['apply_time'] . PHP_EOL;
