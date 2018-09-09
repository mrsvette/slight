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
    $prices = [];
    if ($is_available) {
        $prices = $website_tool->get_prices($params);
    }

    $settings = $this->get('settings');

    $tmodel = new \ExtensionsModel\TldModel();
    $tlds = $tmodel->getRows(['enabled' => 1]);

    return $this->view->render($response, 'cek_domain.phtml', [
        'tlds' => $tlds,
        'params' => $params,
        'is_available' => $is_available,
        'prices' => $prices
    ]);
});

$app->get('/domain/[{name}]', function ($request, $response, $args) {
    $params = $request->getParams();
    if ($args['name'] == 'rumahweb') {
        $url = 'https://order2.'.$args['name'].'.com/?domain='.$params['sld'].$params['tld'].'&reff=domain';
    } elseif ($args['name'] == 'niagahoster') {
        $url = 'https://panel.'.$args['name'].'.co.id/ref/7878?r=orderdomain/searchresults?sld='.$params['sld'].'&tld='.$params['tld'];
    }

    return $response->withRedirect( $url );
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
