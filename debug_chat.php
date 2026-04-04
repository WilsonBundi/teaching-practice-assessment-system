<?php

// Debug script to test ChatService
require_once __DIR__ . '/vendor/autoload.php';

// Include Yii bootstrap
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Basic config for testing
$config = [
    'id' => 'debug-app',
    'basePath' => __DIR__,
    'components' => [
        'db' => require __DIR__ . '/config/db.php',
    ],
];

$app = new \yii\console\Application($config);

// Test ChatService
$chatService = new \app\components\ChatService();

// Load a user
$user = \app\models\Users::findOne(1);

$testMessages = [
    'hey',
    'explain TP E24',
    'how many students',
    'what is assessment'
];

echo "Testing ChatService responses:\n\n";

foreach ($testMessages as $message) {
    echo "Input: '$message'\n";
    try {
        $response = $chatService->generateResponse($message, [], $user);
        echo "Response: $response\n";
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
    echo "\n" . str_repeat('-', 50) . "\n\n";
}
