<?php
$config=require __DIR__ . '/config/db.php';
$pdo=new PDO($config['dsn'],$config['username'],$config['password']);
$stm=$pdo->query("select column_name,data_type from information_schema.columns where table_name='zone' order by ordinal_position");
while($r=$stm->fetch(PDO::FETCH_ASSOC)){
    echo $r['column_name'].' '.$r['data_type'].'\n';
}
