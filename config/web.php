<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'app/site',
    'layout' => 'main',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => []
                ]
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'o60lq8tkQdSpe0YVVgspsSNPn-jIuk1q',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\cms\models\AuthUser',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/main-html',
            'textLayout' => 'layouts/main-text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['tuyenltk7997@gmail.com' => 'Travel Sharing'],
            ],
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // 'host' => 'host07.emailserver.vn',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                // 'username' => 'loitn@hcmgis.vn',
                // 'password' => 'loitnTravel Sharing',
                // 'port' => '465', // Port 25 is a very common port too
                // 'encryption' => 'ssl', // It is often used, check your provider or mail server specs
            ],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'app\controllers\CustomRule',
                ]
            ],
        ],
        'db' => $db,
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '235775407624542',
                    'clientSecret' => '236068a2f1f6ed0268f3803c49ed9233',
                ],
                'google' => [
                    'class'        => 'yii\authclient\clients\Google',
                    'clientId'     => '636609541277-j7v6uno06bn8undsktcaoafnt9e9ho0m.apps.googleusercontent.com',
                    'clientSecret' => 'CWigrOdrN6C0q_648_vuav_G',
                ],
            ],
        ],
    ],
    'as beforeRequest' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'controllers' => ['site', 'app/site', 'app/place', 'app/destination', 'app/plan'],
                'allow' => true,
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$config['modules']['contrib'] = [
    'class' => 'app\modules\contrib\Module'
];

$config['modules']['cms'] = [
    'class' => 'app\modules\cms\Module'
];

$config['modules']['app'] = [
    'class' => 'app\modules\app\Module'
];

return $config;
