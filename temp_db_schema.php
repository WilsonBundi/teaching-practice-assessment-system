<?php
$config = require __DIR__ . '/config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);
$tables = ['users', 'school', 'learning_area', 'assessment', 'grade'];
foreach ($tables as $t) {
    echo "-- $t --\n";
    $stm = $pdo->query("select column_name, data_type from information_schema.columns where table_name = '$t' order by ordinal_position");
    foreach ($stm as $r) {
        echo $r['column_name'] . ' (' . $r['data_type'] . ')\n';
    }
}
