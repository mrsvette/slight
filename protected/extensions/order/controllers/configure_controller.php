<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class ConfigureController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET', 'POST'], '/[{name}]', [$this, 'product']);
    }

    public function product($request, $response, $args)
    {
        $model = new \ExtensionsModel\ClientOrderModel('create');
        $product = \ExtensionsModel\ProductModel::model()->findByAttributes( ['slug'=>$args['name']] );
        if (!$product instanceof \RedBeanPHP\OODBBean) {
            return $this->_container->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found!');
        }

        return $this->_container->view->render($response, 'order/configure_product.phtml', [
            'model' => $model,
            'product' => $product
        ]);
    }
}