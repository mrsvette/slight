<?php
// Routes
require $settings['settings']['admin']['path'] . '/controllers/routes.php';

$app->get('/[{name}]', function ($request, $response, $args) {
    
	if (empty($args['name']))
		$args['name'] = 'index';

    $model = new \Model\PostModel();
    /*if (!file_exists($theme['path'].'/'.$theme['name'].'/views/'.$args['name'].'.phtml')) {
        $data = $model->getPost($args['name']);

        if (empty($data['id'])) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found!');
        }

        return $this->view->render($response, 'post.phtml', [
            'data' => $data,
            'mpost' => $model
        ]);
    }*/

    if (isset($_GET['e']) && $_GET['e'] > 0) {
        $settings = $this->get('settings');
        $view_path = $settings['theme']['path'] . '/' . $settings['theme']['name'] . '/views';
        if (file_exists($view_path.'/'.$args['name'] . '.phtml')) {
            if (file_exists($view_path.'/'.$args['name'] . '.ehtml')) {
                unlink($view_path.'/'.$args['name'] . '.ehtml');
            }
            $cp = copy($view_path.'/'.$args['name'] . '.phtml', $view_path.'/'.$args['name'] . '.ehtml');
            if ($cp) {
                $content = file_get_contents($view_path.'/'.$args['name'] . '.ehtml');
                $parsed_content = str_replace(array("{{", "}}"), array("[[", "]]"), $content);

                $update = file_put_contents($view_path.'/'.$args['name'] . '.ehtml', $parsed_content);
            }
            //var_dump($args['name'] . '.ehtml'); exit;
            return $this->view->render($response, $args['name'] . '.ehtml', [
                'name' => $args['name'],
                'mpost' => $model,
                'request' => $_GET
            ]);
        }
    }

    return $this->view->render($response, $args['name'] . '.phtml', [
        'name' => $args['name'],
        'mpost' => $model,
        'request' => $_GET
    ]);
});

$app->get('/blog/[{name}]', function ($request, $response, $args) {

    if (empty($args['name']))
        $args['name'] = 'index';

    $theme = $this->settings['theme'];
    $model = new \Model\PostModel();
    if (!file_exists($theme['path'].'/'.$theme['name'].'/views/'.$args['name'].'.phtml')) {
        $data = $model->getPost($args['name']);

        if (empty($data['id'])) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found!');
        }

        return $this->view->render($response, 'post.phtml', [
            'data' => $data,
            'mpost' => $model
        ]);
    }

    return $this->view->render($response, $args['name'] . '.phtml', [
        'name' => $args['name'],
        'mpost' => $model
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
