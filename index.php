<?php
require __DIR__ . '/vendor/autoload.php';

// Create Slim app
$app = new \Slim\App();

// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('/opt/lampp/htdocs/slightsite/views', [
        'cache' => '/opt/lampp/htdocs/slightsite/assets'
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

// Register routes
require __DIR__ . '/controllers/routes.php';

// Run app
$app->run();
