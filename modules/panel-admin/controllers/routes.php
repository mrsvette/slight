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
        $username = strtolower($_POST['LoginForm']['username']);
        $model = \Model\AdminModel::model()->findByAttributes(['username'=>$username]);
        if ($model instanceof \RedBeanPHP\OODBBean){
            $has_password = \Model\AdminModel::hasPassword($_POST['LoginForm']['password'], $model->salt);
            if ($model->password == $has_password){
                $login = $user->login($model);
                if ($login){
                    return $response->withRedirect('/panel-admin');
                }
            } else {
                $args['error']['message'] = 'Password yang Anda masukkan salah.';
            }

        }
        $args['error']['message'] = 'User tidak ditemukan';
    }

    return $this->module->render($response, 'login.html', [
        'result' => $args
    ]);
});

$app->get('/panel-admin/logout', function ($request, $response, $args) use ($user) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }
    $logout = $user->logout();
    if ($logout){
        return $response->withRedirect('/panel-admin/login');
    }
});

/**
 * Pages routing sections
 */
$app->get('/panel-admin/pages', function ($request, $response, $args) use ($user, $settings) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

    require_once $settings['settings']['admin']['path']. '/components/tools.php';

    $tools = new \PanelAdmin\Components\AdminTools($settings);

    return $this->module->render($response, 'pages/view.html', [
        'pages' => $tools->getPages()
    ]);
});
$app->get('/panel-admin/pages/update/[{name}]', function ($request, $response, $args) use ($user, $settings) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }
	
    require_once $settings['settings']['admin']['path']. '/components/tools.php';

    $tools = new \PanelAdmin\Components\AdminTools($settings);

    return $this->module->render($response, 'pages/update.html', [
        'data' => $tools->getPage($args['name'])
    ]);
});
$app->post('/panel-admin/pages/update/[{name}]', function ($request, $response, $args) use ($user, $settings) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

    if (isset($_POST['content']) && file_exists($_POST['path'])){
		$update = file_put_contents($_POST['path'], $_POST['content']);
		if ($update) {
			$message = 'Your data is successfully updated.';
			$success = true;
		} else {
			$message = 'Failed to update the page.';
			$success = false;
		}
    }

    require_once $settings['settings']['admin']['path']. '/components/tools.php';

    $tools = new \PanelAdmin\Components\AdminTools($settings);

    return $this->module->render($response, 'pages/update.html', [
        'data' => $tools->getPage($args['name']),
		'message' => ($message) ? $message : null,
		'success' => $success
    ]);
});

/*foreach (glob(__DIR__.'/*Controller.php') as $controller) {
	$cname = basename($controller, '.php');
	if (!empty($cname)) {
		$name = explode("Controller", $cname);
		require_once $controller;
		$app->get('/panel-admin/'.strtolower($name[0]).'/[{name}]', 'PagesController::action');
	}
}*/

$app->get('/panel-admin/pages/create', function ($request, $response, $args) use ($user) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

    return $this->module->render($response, 'pages/create.html');
});

$app->post('/panel-admin/pages/create', function ($request, $response, $args) use ($user, $settings) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

    require_once $settings['settings']['admin']['path']. '/components/tools.php';

    $tools = new \PanelAdmin\Components\AdminTools($settings);
    if (isset($_POST['content'])){
		$create = $tools->createPage($_POST);
		if ($create) {
			$message = 'Your page is successfully created.';
			$success = true;
		} else {
			$message = 'Failed to create new page.';
			$success = false;
		}
    }

    return $this->module->render($response, 'pages/create.html', [
		'message' => ($message) ? $message : null,
		'success' => $success
    ]);
});

$app->post('/panel-admin/pages/delete/[{name}]', function ($request, $response, $args) use ($user, $settings) {
    if ($user->isGuest()){
        return $response->withRedirect('/panel-admin/login');
    }

	if (!isset($args['name'])) {
		return false;
	}

    require_once $settings['settings']['admin']['path']. '/components/tools.php';

    $tools = new \PanelAdmin\Components\AdminTools($settings);
    $delete = $tools->deletePage($args['name']);
	if ($delete) {
		$message = 'Your page is successfully created.';
		echo true;
	}
});
?>
