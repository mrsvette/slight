<?php
return [
    'settings' => [
		'basePath' => realpath(dirname(__DIR__)), // base path of the application
		'name' => 'Slight Site', // the site name
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
		// Cached path
		'cache' => [
			'path' => __DIR__ . '/../../assets',
		],
		// Theme settings
		'theme' => [
			'name' => \Components\Application::getTheme(), //'default',
			'path' => __DIR__ . '/../../themes'
		],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../runtime/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
		// Admin module
		'admin' => [
			'name' => 'panel-admin',
			'path' => realpath(dirname(__DIR__)) . '/modules/panel-admin'
		],
		// Database configuration
		'db' => [
			'connectionString' => 'mysql:host=localhost;dbname=slightsite',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'p@55w0rd',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		],
    ],
];
