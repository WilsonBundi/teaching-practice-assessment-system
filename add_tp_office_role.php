<?php

// Script to add TP Office role
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$config = require __DIR__ . '/config/web.php';
new yii\web\Application($config);

// Check if TP Office role exists
$role = \app\models\Role::findOne(['role_name' => 'TP Office']);
if (!$role) {
    $role = new \app\models\Role();
    $role->role_name = 'TP Office';
    if ($role->save()) {
        echo "TP Office role added successfully with ID: " . $role->role_id . "\n";
    } else {
        echo "Failed to add TP Office role: " . implode(', ', $role->getErrorSummary(true)) . "\n";
    }
} else {
    echo "TP Office role already exists with ID: " . $role->role_id . "\n";
}