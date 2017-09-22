<?php

namespace Extensions\Controllers;

use Extensions\Components\ClientBaseController as ClientBaseController;

class ConfigureController extends ClientBaseController
{
    public function __construct($app, $client)
    {
        parent::__construct($app, $client);
    }

    public function register($app)
    {
        $app->map(['GET', 'POST'], '/signup', [$this, 'signup']);
        $app->map(['GET', 'POST'], '/[{name}]', [$this, 'configure']);
    }

    public function configure($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect('/order/configure/signup?p='.$args['name']);
        }

        $model = new \ExtensionsModel\ClientOrderModel('create');
        $product = \ExtensionsModel\ProductModel::model()->findByAttributes( ['slug'=>$args['name']] );
        if (!$product instanceof \RedBeanPHP\OODBBean) {
            return $this->_container->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found!');
        }

        return $this->_container->view->render($response, 'order/configure_site.phtml', [
            'model' => $model,
            'product' => $product
        ]);
    }

    public function signup($request, $response, $args)
    {
        $model = new \ExtensionsModel\ClientOrderModel('create');

        if (isset($_POST['Client'])) {
            $client = new \ExtensionsModel\ClientModel('create');
            $client->email = $_POST['Client']['email'];
            $client->name = $_POST['Client']['name'];
            $client->salt = md5(uniqid());
            $client->password = $client->hasPassword($_POST['Client']['password'], $client->salt);
            $client->status = 'active';
            $client->client_group_id = 1;
            $client->created_at = date("Y-m-d H:i:s");
            $client->updated_at = date("Y-m-d H:i:s");
            $save = \ExtensionsModel\ClientModel::model()->save(@$client);
            if ($save) {
                $message = 'Data Anda telah berhasil disimpan.';
                $success = true;
                $login = $this->_user->login($client);
                if ($login)
                    return $response->withRedirect('/order/configure/'.$_GET['p']);
            } else {
                $message = \ExtensionsModel\ClientModel::model()->getErrors(false);
                $errors = \ExtensionsModel\ClientModel::model()->getErrors(true, true);
                $success = false;
            }
        }

        return $this->_container->view->render($response, 'order/signup.phtml', [
            'model' => $model,
            'message' => (!empty($message))? $message : null,
            'success' => (!empty($success))? $success : null,
            'errors' => (!empty($errors))? $errors : null,
            'client' => (!empty($_POST))? $_POST['Client'] : null
        ]);
    }
}