<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class UsersController extends BaseController
{
    protected $_login_url = '/panel-admin/default/login';

    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/create', [$this, 'create']);
        $app->map(['GET', 'POST'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $models = \Model\AdminModel::model()->findAll();

        return $this->_container->module->render($response, 'users/view.html', [
            'models' => $models,
            'cmodel' => new \Model\AdminModel(),
        ]);
    }

    public function create($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $model = new \Model\AdminModel('create');

        if (isset($_POST['Admin'])){
            $model->username = $_POST['Admin']['username'];
            $model->salt = md5(uniqid());
            $model->password = $_POST['Admin']['password'];
            //$model->password = $model->hasPassword($_POST['Admin']['password'], $model->salt);
            $model->username = $_POST['Admin']['username'];
            $model->email = $_POST['Admin']['email'];
            $model->group_id = $_POST['Admin']['group_id'];
            $model->status = $_POST['Admin']['status'];
            $model->created_at = date('Y-m-d H:i:s');
            $create = \Model\AdminModel::model()->save($model);
            if ($create) {
                $bean = \Model\AdminModel::model()->findByAttributes(['username'=>$model->username]);
                $bean->password = $model->hasPassword($model->password, $model->salt);
                $update = \Model\AdminModel::model()->update($bean, false);

                $message = 'Your data is successfully created.';
                $success = true;
            } else {
                $message = \Model\AdminModel::model()->getErrors(false);
                $errors = \Model\AdminModel::model()->getErrors(true, true);
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'users/create.html', [
            'model' => $model,
            'message' => ($message) ? $message : null,
            'success' => $success,
            'errors' => $errors
        ]);
    }

    public function update($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $model = \Model\AdminModel::model()->findByPk($args['id']);

        if (isset($_POST['Admin'])){
            $model->username = $_POST['Admin']['username'];
            $model->email = $_POST['Admin']['email'];
            $model->group_id = $_POST['Admin']['group_id'];
            $model->status = $_POST['Admin']['status'];
            $update = \Model\AdminModel::model()->update($model);
            if ($update) {
                $message = 'Your data is successfully updated.';
                $success = true;
            } else {
                $message = \Model\AdminModel::model()->getErrors(false);
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'users/update.html', [
            'model' => $model,
            'admin' => new \Model\AdminModel(),
            'message' => ($message) ? $message : null,
            'success' => $success
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

        $model = \Model\AdminModel::model()->findByPk($args['id']);
        $delete = \Model\AdminModel::model()->delete($model);
        if ($delete) {
            $message = 'Your data is successfully deleted.';
            echo true;
        }
    }
}