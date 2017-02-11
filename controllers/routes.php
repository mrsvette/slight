<?php
// Routes

/*$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/
// Define named route
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.phtml', [
        'name' => $args['name']
    ]);
});
