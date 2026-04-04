<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);
echo 'Zones:' . PHP_EOL;
foreach ($pdo->query('SELECT * FROM zone') as $r) echo $r['zone_id'] . ': ' . $r['zone_name'] . PHP_EOL;
echo 'Schools:' . PHP_EOL;
foreach ($pdo->query('SELECT school_id, school_name, zone_id FROM school') as $r) echo $r['school_id'] . ': ' . $r['school_name'] . ' (zone ' . $r['zone_id'] . ')' . PHP_EOL;
