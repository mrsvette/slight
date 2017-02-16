<?php
// panel-admin routes
$app->get('/panel-admin', function ($request, $response, $args) use ($user) {
	if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

	return $this->module->render($response, 'index.html', [
        'name' => $args['name']
    ]);
});

$app->get('/panel-admin/login', function ($request, $response, $args) use ($user) {
    if (!$user->isGuest()){
        return $response->withRedirect('/panel-admin');
    }

    return $this->module->render($response, 'login.html', [
        'name' => $args['name']
    ]);
});

$app->post('/panel-admin/login', function ($request, $response, $args) use ($user) {
    if (!$user->isGuest()){
        return $response->withRedirect('/panel-admin');
    }

    if (isset($_POST['LoginForm'])){
        var_dump($_POST['LoginForm']); exit;
    }

    return $this->module->render($response, 'login.html', [
        'name' => $args['name']
    ]);
});
?>
