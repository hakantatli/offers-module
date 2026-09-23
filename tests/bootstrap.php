<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
$config['components']['request']['hostInfo'] = 'http://localhost';
$config['components']['request']['scriptUrl'] = '/index.php';

new yii\web\Application($config);
