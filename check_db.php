<?php
// Bootstrap Yii
require_once 'vendor/autoload.php';
require_once 'vendor/yiisoft/yii2/Yii.php';

$config = require 'config/web.php';
$app = new \yii\web\Application($config);

// Now we can use Yii components
$db = Yii::$app->db;

// Check assessments
$assessments = $db->createCommand('SELECT assessment_id, student_reg_no, assessment_date FROM assessment LIMIT 5')->queryAll();
echo "Assessments found: " . count($assessments) . "\n";
foreach ($assessments as $assessment) {
    echo "- ID: {$assessment['assessment_id']}, Student: {$assessment['student_reg_no']}, Date: {$assessment['assessment_date']}\n";
}

// Check competence areas
$competenceAreas = $db->createCommand('SELECT competence_id, competence_name FROM competence_area ORDER BY competence_id')->queryAll();
echo "\nCompetence Areas found: " . count($competenceAreas) . "\n";
foreach ($competenceAreas as $area) {
    echo "- ID: {$area['competence_id']}, Name: {$area['competence_name']}\n";
}
?>