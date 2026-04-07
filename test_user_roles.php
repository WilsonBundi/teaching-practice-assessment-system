<?php
require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';

$cfg = require 'config/db.php';
$db = new PDO($cfg['dsn'], $cfg['username'], $cfg['password']);
$stmt = $db->query('SELECT user_id, username, role_id FROM users ORDER BY user_id LIMIT 20');
foreach ($stmt as $row) {
    echo $row['user_id'] . ' | ' . $row['username'] . ' | ' . $row['role_id'] . PHP_EOL;
}
