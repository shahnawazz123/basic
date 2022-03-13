<?php
use \yii\web\Request;
$functions = require(__DIR__ . '/functions.php');
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$baseUrl = str_replace('/web', '', (new Request)->getBaseUrl());

$config = [
    'id' => 'eyadat',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'AKtUW9XRELi6MlM7LGjR8JecaPE0OIPL',
            'baseUrl' => $baseUrl,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null, 
                    'css' => [$baseUrl.'/theme/plugins/bootstrap/dist/css/bootstrap.min.css'],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null, 
                    'js' => [$baseUrl.'/theme/plugins/bootstrap/dist/js/bootstrap.min.js'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'mywebapp61@gmail.com',//'noreplyedayat@gmail.com',
                'password' => 'qegtcmnrwahrookt',//'edayat@2021',
                'port' => '587',
                'encryption' => 'tls',
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'listing/category/<category_id:[-a-zA-Z-0-9-.]+>/<title:[-a-zA-Z-0-9-.]+>' => 'site/listing'
            ],
        ],
        'auth' => [
            'class' => 'app\components\Auth',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
                    'clientId' => '1865339173767070',
                    'clientSecret' => '68bc634b9f77cef2ae15bb8032ba2f58',
                    'attributeNames' => ['name', 'email', 'first_name', 'last_name', 'gender'],
                    'title' => 'Login'
                ],
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
        'treemanager' => [
            'class' => 'app\modules\treemanager\Module',
            //'class' => '\app\components\Module',
            'treeViewSettings' => [
                'nodeView' => '@app/views/category/_treeview'
            ]
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'gridview' => ['class' => 'kartik\grid\Module']
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'crud' => [
                'class' => 'app\giiTemplates\crud\Generator',
                //'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'default' => '@app/giiTemplates/crud/default',
                ]
            ]
        ],
    ];
}

return $config;
