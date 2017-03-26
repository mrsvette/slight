<?php
// Routes
require $settings['settings']['admin']['path'] . '/controllers/routes.php';

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");

	if (empty($args['name']))
		$args['name'] = 'index';

    return $this->view->render($response, $args['name'] . '.phtml', [
        'name' => $args['name']
    ]);
});

$app->post('/kontak-kami', function ($request, $response, $args) {
    $message = 'Pesan Anda gagal dikirimkan.';
    if (isset($_POST['Contact'])){
        //send mail to admin
        $message = 'Pesan Anda berhasil dikirim. Kami akan segera merespon pesan Anda.';
    }

    echo $message; exit;
});
