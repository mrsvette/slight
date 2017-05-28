<?php
// Fetch DI Container
$container = $app->getContainer();

// User identity
require __DIR__ . '/identity.php';
$user = new \Components\UserIdentity($app);

// Controller
require __DIR__ . '/controller.php';

//trailling slash
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Register Twig View helper
$container['view'] = function ($c) {
	$settings = $c->get('settings');

	$view_path = $settings['theme']['path'] . '/' . $settings['theme']['name'] . '/views';
    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path'],
        'auto_reload' => true,
    ]);

    addFilter($view->getEnvironment(), $c);
    addGlobal($view->getEnvironment(), $c);

    return $view;
};

// Register Twig View module
$container['module'] = function ($c) use ($user) {
	$settings = $c->get('settings');
	$view_path = $settings['admin']['path'] . '/views';

    $view = new \Slim\Views\Twig( $view_path , [
        'cache' => $settings['cache']['path'],
        'auto_reload' => true,
    ]);

    addFilter($view->getEnvironment(), $c);
    addGlobal($view->getEnvironment(), $c, $user);

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
    $uri = $c['request']->getUri();
    $base_url = $uri->getScheme().'://'.$uri->getHost().$uri->getBasePath();
    if (!empty($uri->getPort()))
        $base_url .= ':'.$uri->getPort();

    $admin_module = $c->get('settings')['admin']['name'];
    $theme = $c->get('settings')['theme']['name'];

    $filters = [
        new \Twig_SimpleFilter('dump', function ($string) {
            return var_dump($string);
        }),
        new \Twig_SimpleFilter('link', function ($string) use ($base_url) {
            return $base_url .'/'. $string;
        }),
        new \Twig_SimpleFilter('asset_url', function ($string) use ($base_url, $theme){
            return $base_url .'/../themes/'. $theme .'/assets/'. $string;
        }),
        new \Twig_SimpleFilter('admin_asset_url', function ($string) use ($base_url, $admin_module) {
            return $base_url .'/modules/'. $admin_module .'/assets/'. $string;
        }),
        new \Twig_SimpleFilter('alink', function ($string) use ($base_url, $admin_module) {
            return $base_url .'/'. $admin_module. '/' .$string;
        }),
    ];

    foreach ($filters as $i => $filter) {
        $env->addFilter($filter);
    }
}

// global variable
function addGlobal($env, $c, $user = null)
{
    $uri = $c['request']->getUri();
    $setting = $c->get('settings');
    $base_url = $uri->getScheme().'://'.$uri->getHost().$uri->getBasePath();
    if (!empty($uri->getPort()))
        $base_url .= ':'.$uri->getPort();

    $globals = [
        'name' => $setting['name'],
        'baseUrl' => (!defined('BASE_URL')) ? $base_url : BASE_URL,
        'basePath' => $setting['basePath'],
        'adminBasePath' => $setting['admin']['path'],
        'user' => $user
    ];

    $env->addGlobal('App', $globals);
}
