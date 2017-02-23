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
?>
