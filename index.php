<?php
require __DIR__ . '/vendor/autoload.php';

// Main Configuration
$settings = require __DIR__ . '/configs/main.php';

// Create Slim app
$app = new \Slim\App($settings);

// Dependencies
require __DIR__ . '/components/dependencies.php';

// Register routes
require __DIR__ . '/controllers/routes.php';

// Run app
$app->run();
