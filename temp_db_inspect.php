<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'],$config['username'],$config['password']);
$stmt = $pdo->query("select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE'");
foreach ($stmt as $r) {
    echo $r['table_name'] . PHP_EOL;
}
