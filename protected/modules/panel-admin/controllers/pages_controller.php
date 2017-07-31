<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class PagesController extends BaseController
{
    
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/create', [$this, 'create']);
        $app->map(['GET', 'POST'], '/update/[{name}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{name}]', [$this, 'delete']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);
        
        return $this->_container->module->render($response, 'pages/view.html', [
            'pages' => $tools->getPages()
        ]);
    }

    public function create($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

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

        return $this->_container->module->render($response, 'pages/create.html', [
            'message' => ($message) ? $message : null,
            'success' => $success
        ]);
    }

    public function update($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
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

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

        return $this->_container->module->render($response, 'pages/update.html', [
            'data' => $tools->getPage($args['name']),
            'message' => ($message) ? $message : null,
            'success' => $success
        ]);
    }

    public function delete($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (!isset($args['name'])) {
            return false;
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);
        $delete = $tools->deletePage($args['name']);
        if ($delete) {
            $message = 'Your page is successfully created.';
            echo true;
        }
    }
}