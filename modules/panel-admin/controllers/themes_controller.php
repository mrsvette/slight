<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class ThemesController extends BaseController
{
    protected $_login_url = '/panel-admin/default/login';

    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/update', [$this, 'update']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

        return $this->_container->module->render($response, 'themes/view.html', [
            'themes' => $tools->getThemes(),
            'current_theme' => $this->_settings['theme']['name']
        ]);
    }

    public function update($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

        if (isset($_POST['id'])){
            if ($_POST['install'])
                $update = $tools->updateTheme($_POST['id']);
            else
                $update = $tools->updateTheme('default');
            
            if ($update) {
                $message = ($_POST['install'] > 0)? 'Your theme is successfully updated to '.$_POST['id'] : 'Succesfully uninstall '.$_POST['id'].' theme.';
                $success = true;
            } else {
                $message = 'Failed to set the theme.';
                $success = false;
            }

            return json_encode(['success'=>$success, 'message'=>$message]);
        }
    }
}