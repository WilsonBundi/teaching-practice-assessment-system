<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'],$config['username'],$config['password']);

$tables = ['role', 'users', 'school', 'learning_area', 'assessment', 'grade'];
foreach ($tables as $t) {
    echo "-- $t --\n";
    $stm = $pdo->query("select * from $t limit 5");
    foreach ($stm as $r) {
        print_r($r);
    }
}
