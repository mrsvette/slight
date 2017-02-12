<?php
// Routes
$app->get('/admin', function ($request, $response, $args) {
	
	return $this->module->render($response, 'index.html', [
        'name' => $args['name']
    ]);
});

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");

	if (empty($args['name']))
		$args['name'] = 'index';

    return $this->view->render($response, $args['name'] . '.html', [
        'name' => $args['name']
    ]);
});
