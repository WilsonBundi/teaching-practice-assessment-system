<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$config = require __DIR__ . '/config/web.php';
new yii\web\Application($config);

$svc = new app\components\ChatService();
$user = app\models\Users::findOne(1);

// Accept message by CLI argument (development mode) or prompt
$message = isset($argv[1]) && trim($argv[1]) !== '' ? $argv[1] : null;

if (!$message) {
    echo "Usage: php debug_chat2.php \"your question here\"\n";
    exit(1);
}

$response = $svc->generateResponse($message, [], $user, null);
echo "User Input: $message\n";
echo "Bot Response:\n$response\n";
