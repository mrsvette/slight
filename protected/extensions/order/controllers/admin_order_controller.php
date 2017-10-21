<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class AdminOrderController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $orders = \ExtensionsModel\ClientOrderModel::model()->findAll();


        return $this->_container->module->render($response, 'orders/view.html', [
            'orders' => $orders
        ]);
    }

    public function update($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $model = \ExtensionsModel\ClientOrderModel::model()->findByPk($args['id']);


        return $this->_container->module->render($response, 'orders/update.html', [
            'model' => $model
        ]);
    }

    public function delete($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\ClientOrderModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\ClientOrderModel::model()->delete($model);
        if ($delete) {
            echo true;
        }
    }
}