<?php
require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@app', __DIR__);
Yii::setAlias('@runtime', __DIR__.'/runtime');
Yii::setAlias('@webroot', __DIR__.'/web');
Yii::setAlias('@web', '/');

$config = require 'config/web.php';
$app = new \yii\web\Application($config);

try {
    echo "Testing model counts:\n";
    echo "Schools: " . \app\models\School::find()->count() . "\n";
    echo "Zones: " . \app\models\Zone::find()->count() . "\n";
    echo "Grades: " . \app\models\Grade::find()->count() . "\n";
    echo "Learning Areas: " . \app\models\LearningArea::find()->count() . "\n";
    echo "Strands: " . \app\models\Strand::find()->count() . "\n";
    echo "Substrands: " . \app\models\Substrand::find()->count() . "\n";
    echo "All models working correctly!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}