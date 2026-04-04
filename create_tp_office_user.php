<?php

// Script to create a test TP Office user
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$config = require __DIR__ . '/config/web.php';
new yii\web\Application($config);

// Check if TP Office role exists
$role = \app\models\Role::findOne(['role_name' => 'TP Office']);
if (!$role) {
    echo "TP Office role not found. Please run add_tp_office_role.php first.\n";
    exit(1);
}

// Check if test user exists
$user = \app\models\Users::findOne(['username' => 'tpoffice']);
if (!$user) {
    $user = new \app\models\Users();
    $user->username = 'tpoffice';
    $user->password = 'tpoffice'; // Plain text for demo
    $user->name = 'TP Office User';
    $user->role_id = $role->role_id;
    $user->status = 'active';
    
    if ($user->save()) {
        echo "Test TP Office user created successfully!\n";
        echo "Username: tpoffice\n";
        echo "Password: tpoffice\n";
        echo "Role: TP Office\n";
    } else {
        echo "Failed to create user: " . implode(', ', $user->getErrorSummary(true)) . "\n";
    }
} else {
    echo "Test TP Office user already exists.\n";
    echo "Username: tpoffice\n";
    echo "Password: tpoffice\n";
}