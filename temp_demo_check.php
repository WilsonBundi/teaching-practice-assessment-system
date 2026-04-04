<?php
$config=require __DIR__.'/config/db.php';
$pdo=new PDO($config['dsn'],$config['username'],$config['password']);
$counts=['users'=>'SELECT COUNT(*) FROM users', 'schools'=>'SELECT COUNT(*) FROM school', 'assessments'=>'SELECT COUNT(*) FROM assessment', 'grades'=>'SELECT COUNT(*) FROM grade'];
foreach($counts as $k=>$sql){ $val=$pdo->query($sql)->fetchColumn(); echo "$k: $val\n"; }
$one=$pdo->query("SELECT assessment_id, student_reg_no, overall_level FROM assessment ORDER BY assessment_date desc LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($one);
