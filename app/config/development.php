<?php

return [
    'app' => [
        'url' => 'http://192.168.1.195',
        'hash' => [
            'algo' => PASSWORD_BCRYPT,
            'cost' => 10
        ]
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'name' => 'document_manager',
        'username' => 'cs546',
        'password' => 'cs546',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ],
    'auth' => [
        'session' => 'user_id',
    ],
    'mail' => [
        'smtp_auth' => true,
        'smtp_secure' => 'tls',
        'host' => 'smtp.gmail.com',
        'username' => '',
        'password' => '',
        'port' => 587,
        'html' => true
    ],
    'twig' => [
        'debug' => true
    ],
    'csrf' => [
        'session' => 'csrf_token',
        'key' => 'csrf_key'
    ]
];
