<?php
// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
	$settings = $c->get('settings');

	$view_path = $settings['theme']['path'] . '/' . $settings['theme']['name'] . '/views';
    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path'],
        'auto_reload' => true,
    ]);

    // Instantiate and add Slim specific extension
    /*$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));*/

    addFilter($view->getEnvironment(), $c);
    addGlobal($view->getEnvironment(), $c);

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

// filter
function addFilter($env, $c)
{
    if(!defined('BASE_URL')){
        $uri = $c['request']->getUri();
        define('BASE_URL', $uri->getScheme().'://'.$uri->getHost().$uri->getBasePath());
    }

    if(!defined('THEME')){
        $uri = $c['request']->getUri();
        define('THEME', $c->get('settings')['theme']['name']);
    }

    $filters = [
        new \Twig_SimpleFilter('dump', function ($string) {
            return var_dump($string);
        }),
        new \Twig_SimpleFilter('link', function ($string) {
            return BASE_URL .'/'. $string;
        }),
        new \Twig_SimpleFilter('asset_url', function ($string) {
            return BASE_URL .'/themes/'. THEME .'/assets/'. $string;
        }),
    ];

    foreach ($filters as $i => $filter) {
        $env->addFilter($filter);
    }
}

// global variable
function addGlobal($env, $c)
{
    $uri = $c['request']->getUri();
    $setting = $c->get('settings');
    $globals = [
        'name' => $setting['name'],
        'baseUrl' => (!defined('BASE_URL')) ? $uri->getScheme().'://'.$uri->getHost().$uri->getBasePath() : BASE_URL,
        'basePath' => $setting['basePath'],
    ];

    $env->addGlobal('App', $globals);
}
