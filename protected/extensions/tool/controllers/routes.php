<?php
$app->get('/cek-domain', function ($request, $response, $args) {
    $tmodel = new \ExtensionsModel\TldModel();
    $tlds = $tmodel->getRows(['enabled' => 1]);

    return $this->view->render($response, 'cek_domain.phtml', [
        'tlds' => $tlds
    ]);
});
$app->post('/cek-domain', function ($request, $response, $args) {
    $params = $request->getParams();

    $website_tool = new \Extensions\Components\WebsiteTool();
    $is_available = $website_tool->check_availability($params);

    $settings = $this->get('settings');

    $tmodel = new \ExtensionsModel\TldModel();
    $tlds = $tmodel->getRows(['enabled' => 1]);

    return $this->view->render($response, 'cek_domain.phtml', [
        'tlds' => $tlds,
        'params' => $params,
        'is_available' => $is_available
    ]);
});

foreach (glob(__DIR__.'/*_controller.php') as $controller) {
	$cname = basename($controller, '.php');
	if (!empty($cname)) {
		require_once $controller;
	}
}

foreach (glob(__DIR__.'/../components/*.php') as $component) {
    $cname = basename($component, '.php');
    if (!empty($cname)) {
        require_once $component;
    }
}

?>
