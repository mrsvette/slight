<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class DefaultController extends BaseController
{
    protected $_login_url = '/panel-admin/default/login';

    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET', 'POST'], '/login', [$this, 'login']);
        $app->map(['GET'], '/logout', [$this, 'logout']);
    }

    public function login($request, $response, $args)
    {
        if (!$this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (isset($_POST['LoginForm'])){
            $username = strtolower($_POST['LoginForm']['username']);
            $model = \Model\AdminModel::model()->findByAttributes(['username'=>$username]);
            if ($model instanceof \RedBeanPHP\OODBBean){
                $has_password = \Model\AdminModel::hasPassword($_POST['LoginForm']['password'], $model->salt);
                if ($model->password == $has_password){
                    $login = $this->_user->login($model);
                    if ($login){
                        return $response->withRedirect('/panel-admin');
                    }
                } else {
                    $args['error']['message'] = 'Password yang Anda masukkan salah.';
                }

            }
            $args['error']['message'] = 'User tidak ditemukan';
        }

        return $this->_container->module->render($response, 'default/login.html', [
            'result' => $args
        ]);
    }

    public function logout($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $logout = $this->_user->logout();
        if ($logout){
            return $response->withRedirect($this->_login_url);
        }
    }
}