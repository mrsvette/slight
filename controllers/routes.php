<?php
// Routes
$app->get('/', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");

    return $this->view->render($response, 'index.phtml', [
        'name' => $args['name']
    ]);
});
