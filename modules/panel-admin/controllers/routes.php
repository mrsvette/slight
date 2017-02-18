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
                var_dump(true); exit;
            }

        }


    }

    return $this->module->render($response, 'login.html', [
        'name' => $args['name']
    ]);
});
?>