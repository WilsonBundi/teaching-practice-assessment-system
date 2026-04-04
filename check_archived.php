<?php
$config = require 'config/db.php';
$db = new PDO($config['dsn'], $config['username'], $config['password']);
$result = $db->query('SELECT assessment_id, archived FROM assessment LIMIT 5');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo 'ID: ' . $row['assessment_id'] . ' Archived: ' . ($row['archived'] ?? 'NULL') . PHP_EOL;
}
?>