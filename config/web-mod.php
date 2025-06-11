<?php

$params = require __DIR__.'/params.php';

$config = [
    'id' => 'cati',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'j7mlVOuFDMQewVRgjI--HlJMEI0zYeHd',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            // Whether to automatically renew the identity cookie each time a page is requested.
            'autoRenewCookie' => true,
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true, // Whether to enable cookie-based login.
            // The number of seconds in which the user will be logged out automatically if he remains inactive.
            // Requires enableAutoLogin to be false and to increment session.gc_maxlifetime in PHP conf.
            'authTimeout' => 60 * 60 * 12,  // 12 hours.
            'enableLdap' => true,
            // Whether to use session to persist authentication status across multiple requests.
            'enableSession' => true,
            'loginUrl' => ['/cati-auth/login'],
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',

            // Comment this if you don't want to record user logins
            'on afterLogin' => function ($event) {
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            },
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['coordinadorPlan', 'coorOPresiPlan'],
        ],
        'catilanguage' => [
            'class' => 'app\components\CatilanguageComponent',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            // NOTE: Configure transport as needed.
            // See http://swiftmailer.org/docs/sending.html
            /*
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'localhost',
                'username' => 'username',
                'password' => 'password',
                'port' => '587',
                'encryption' => 'tls',
            ],
            */
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ], [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['gestion'],
                    'logFile' => '@runtime/logs/gestion.log',
                ], [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['coordinadores'],
                    'logFile' => '@runtime/logs/coordinadores.log',
                ],
            ],
        ],
        'db' => require(__DIR__.'/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'language' => 'es',
    'name' => 'Oferta de estudios oficiales universitarios',
    'modules' => [
        'user-management' => [
            'class' => 'webvimark\modules\UserManagement\UserManagementModule',

            // 'enableRegistration' => true,

            // Add regexp validation to passwords. Default pattern does not restrict user and can enter any set of characters.
            // The example below allows user to enter :
            // any set of characters
            // (?=\S{8,}): of at least length 8
            // (?=\S*[a-z]): containing at least one lowercase letter
            // (?=\S*[A-Z]): and at least one uppercase letter
            // (?=\S*[\d]): and at least one number
            // $: anchored to the end of the string

            //'passwordRegexp' => '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$',

            // Here you can set your handler to change layout for any controller or action
            // Tip: you can use this event in any module
            'on beforeAction' => function (yii\base\ActionEvent $event) {
                if ('user-management/auth/login' == $event->action->uniqueId) {
                    $event->action->controller->layout = 'loginLayout.php';
                }
            },
        ],
    ],
    'params' => $params,
    'timeZone' => 'Europe/Madrid',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
	// uncomment the following to add your IP if you are not connecting from localhost.
	'allowedIPs' => ['127.0.0.1', '::1', '155.210.47.26', '155.210.70.41'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
