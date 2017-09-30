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
        $app->map(['POST'], '/theme', [$this, 'theme']);
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

        /*if (isset($_POST['Order'])) {
            $model->client_id = $this->_user->id;
            $model->product_id = $product->id;
            $model->group_id = time();
            $model->group_master = 1;
            $model->invoice_option = \ExtensionsModel\ClientOrderModel::INVOICE_OPTION_NO_INVOICE;
            $model->title = $product->title.' untuk '.$_POST['Order']['site_name'];
            $model->currency = 'IDR';
            $model->service_type = $product->type;
            $model->period = '1M';
            $model->quantity = 1;
            $model->unit = 'product';
            $model->price = 0;
            $model->discount = 0;
            $model->status = \ExtensionsModel\ClientOrderModel::STATUS_PENDING_SETUP;
            $model->config = json_encode($_POST['Order']);
            $model->created_at = date('c');
            $model->updated_at = date('c');
            $save = \ExtensionsModel\ClientOrderModel::model()->save(@$model);
            if ($save) {

            }
        }*/

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

    public function theme($request, $response, $args)
    {
        if (isset($_POST['Order'])) {
            $data = $_POST['Order'];
            $product = \ExtensionsModel\ProductModel::model()->findByAttributes( ['slug'=>$_POST['Order']['slug']] );

            return $this->_container->view->render($response, 'order/themes.phtml', [
                'data' => $data,
                'product' => $product
            ]);
        } else {
            return $response->withRedirect('/');
        }
    }
}