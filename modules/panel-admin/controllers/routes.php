<?php
// panel-admin routes
$app->get('/panel-admin', function ($request, $response, $args) use ($user) {
	if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/default/login');
    }

	return $this->module->render($response, 'default/index.html', [
        'name' => $args['name']
    ]);
});

foreach (glob(__DIR__.'/*_controller.php') as $controller) {
	$cname = basename($controller, '.php');
	if (!empty($cname)) {
		require_once $controller;
	}
}

$app->group('/panel-admin', function () use ($user) {
    $this->group('/default', function() use ($user) {
        new PanelAdmin\Controllers\DefaultController($this, $user);
    });
    $this->group('/pages', function() use ($user) {
        new PanelAdmin\Controllers\PagesController($this, $user);
    });
    $this->group('/themes', function() use ($user) {
        new PanelAdmin\Controllers\ThemesController($this, $user);
    });
});

?>
