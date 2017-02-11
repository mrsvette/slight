<?php

return [
	'base_url' => 'http://localhost/slighsite',
    'logger.app.logfile' => __DIR__ . '/../runtime/app.log',
    'logger.app.level' => \Monolog\Logger::DEBUG,
    'slim' => [
        'debug' => true,
        'templates.path' => __DIR__ . '/../templates',
        'cookies.encrypt' => true,
        'cookies.secret_key' => 'CHANGE_ME',
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    ],
    'twig' => [
        'environment' => array(
            'charset' => 'utf-8',
            'cache' => __DIR__ . '/../templates/cache',
            'auto_reload' => true,
            'strict_variables' => true,
            'autoescape' => true,
            'debug' => true,
        ),
    ],
];
