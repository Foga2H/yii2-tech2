<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'auth' => 'site/login',
                'register' => 'site/register',
                'profile' => 'profile/index',
                'auctions' => 'auction/index',
                'auction-add' => 'auction/add-item',
                'auction-buy' => 'auction/buy-item',
                'animals' => 'animal/index',
                'animal/use-hearts' => 'animal/use-hearts',
                'animal/get-parents/<animal_id:\d+>' => 'animal/get-parents',
                'animal/get-childs/<animal_id:\d+>' => 'animal/get-childs',
                'users' => 'user/index',
                'user/<user_id:\d+>' => 'user/user'
            ],
        ],
    ],
    'params' => $params,
];
