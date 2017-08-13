<?php
// Routes
require $settings['settings']['admin']['path'] . '/controllers/routes.php';

$app->get('/[{name}]', function ($request, $response, $args) {
    
	if (empty($args['name']))
		$args['name'] = 'index';

    $theme = $this->settings['theme'];
    if (!file_exists($theme['path'].'/'.$theme['name'].'/views/'.$args['name'].'.phtml')) {
        $model = new \Model\PostModel();
        $data = $model->getPost($args['name']);
        
        if (empty($data['id'])) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found!');
        }

        return $this->view->render($response, 'post.phtml', [
            'data' => $data
        ]);
    }

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
