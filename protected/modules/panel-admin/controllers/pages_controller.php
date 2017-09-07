<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;
use Prophecy\Exception\Exception;

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
        $app->map(['GET', 'POST'], '/update-visual', [$this, 'update_visual']);
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

    public function update_visual($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        require_once $this->_settings['admin']['path']. '/components/tools.php';
        require_once $this->_settings['admin']['path']. '/components/simple_html_dom.php';
        require_once $this->_settings['admin']['path']. '/components/html_format.php';

        if (isset($_POST['content']) && isset($_POST['path'])){
            $tools = new \PanelAdmin\Components\AdminTools($this->_settings);
            $paths = explode("/", $_POST['path']);
            if (empty($paths[1]))
                $paths[1] = 'index';

            $get_page = $tools->getPage($paths[1], true);
            if (!is_array($get_page))
                return false;

            $html_dom = new \PanelAdmin\Components\DomHelper();

            $class_name = uniqid();
            $current_content = str_replace(array("[[", "]]"), array("{{", "}}"), $get_page['content']);

            $html = $html_dom->str_get_html('<div class="'.$class_name.'">'.$current_content.'</div>');

            foreach ($_POST as $node => $html_data) {
                if (!in_array($node, ['content', 'path'])) {
                    $html->find('.'.$node, 0)->__set('innertext', $html_data);
                }
            }

            $new_content = $html->find('.'.$class_name, 0)->innertext();
            if (empty($new_content))
                return false;

            try {
                $view_path = $this->_settings['theme']['path'] . '/' . $this->_settings['theme']['name'] . '/views';
                $cp = copy($view_path.'/'.$paths[1] . '.phtml', $view_path.'/backup/'.$paths[1] . '.xhtml');

                $format = new \PanelAdmin\Components\Format();
                $new_content = $format->HTML($new_content);

                $update = file_put_contents($view_path.'/'.$paths[1] . '.phtml', $new_content);
                if ($update) {
                    //unlink($view_path.'/staging/'.$paths[1] . '.ehtml');
                }
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            return true;
        }
    }

}