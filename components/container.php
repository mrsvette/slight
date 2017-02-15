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
	$view_path = $settings['admin']['path'] . '/views';

    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path'],
        'auto_reload' => true,
    ]);

    addFilter($view->getEnvironment(), $c);
    addGlobal($view->getEnvironment(), $c);

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
        define('THEME', $c->get('settings')['theme']['name']);
    }

    if(!defined('ADMIN_MODULE')){
        define('ADMIN_MODULE', $c->get('settings')['admin']['name']);
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
        new \Twig_SimpleFilter('admin_asset_url', function ($string) {
            return BASE_URL .'/modules/'. ADMIN_MODULE .'/assets/'. $string;
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
        'adminBasePath' => $setting['admin']['path'],
    ];

    $env->addGlobal('App', $globals);
}
