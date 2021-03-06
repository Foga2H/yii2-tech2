<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'modules' => [
            'gii' => [
                'class' => 'yii\gii\Module',
                // permits any and all IPs
                // you should probably restrict this
                'allowedIPs' => ['*']
            ],
            'debug' => [
                'class' => 'yii\debug\Module',
                // permits any and all IPs
                // you should probably restrict this
                'allowedIPs' => ['*']
            ]
        ]
    ],
];
