<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'revpanda-offers',
    'name' => 'RevPanda Offers',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'revpanda_secret_cookie_validation_key_2026',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'sitemap.xml' => 'site/sitemap',
                'offers' => 'offer/index',
                'casino/<slug:[\w-]+>' => 'offer/casino',
                'offer/<slug:[\w-]+>' => 'offer/view',
                'admin' => 'offer-admin/index',
                'admin/casinos' => 'casino/index',
                'admin/casinos/create' => 'casino/create',
                'admin/casinos/<id:\d+>' => 'casino/view',
                'admin/casinos/<id:\d+>/update' => 'casino/update',
                'admin/casinos/<id:\d+>/delete' => 'casino/delete',
                'admin/offers' => 'offer-admin/index',
                'admin/offers/create' => 'offer-admin/create',
                'admin/offers/<id:\d+>' => 'offer-admin/view',
                'admin/offers/<id:\d+>/update' => 'offer-admin/update',
                'admin/offers/<id:\d+>/delete' => 'offer-admin/delete',
                'login' => 'site/login',
                'logout' => 'site/logout',
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                    ],
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
