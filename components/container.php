<?php
// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
	$settings = $c->get('settings');
	$view_path = $settings['theme']['path'] . '/' . $settings['theme']['name'] . '/views';
    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path']
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

// Register Twig View module
$container['module'] = function ($c) {
	$settings = $c->get('settings');
	$view_path = $settings['basePath'] . '/modules/admin/views';

    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path']
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
