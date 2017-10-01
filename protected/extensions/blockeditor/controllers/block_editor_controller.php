<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class BlockEditorController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET', 'POST'], '/update/elements/thumbs/[{name}]', [$this, 'thumbs']);
        $app->map(['GET', 'POST'], '/update/elements/original/[{name}]', [$this, 'original']);
        $app->map(['GET', 'POST'], '/update/elements/css/[{name}]', [$this, 'css']);
        $app->map(['GET', 'POST'], '/update/elements/[{name}]', [$this, 'elements']);
        $app->map(['GET', 'POST'], '/update/bundles/[{name}]', [$this, 'bundles']);
        $app->map(['GET', 'POST'], '/update/[{name}]', [$this, 'update']);
    }

    public function thumbs($request, $response, $args)
    {
        return $response->withRedirect($this->_settings['params']['site_url'].'/themes/'.$this->_settings['params']['theme'].'/views/staging/thumbs/'.$args['name']);
    }

    public function original($request, $response, $args)
    {
        return $this->_container->view->render($response, 'staging/original/'.$args['name'], [
            'args' => $args
        ]);
    }

    public function css($request, $response, $args)
    {
        return $response->withRedirect($this->_settings['params']['site_url'].'/themes/'.$this->_settings['params']['theme'].'/assets/build/elements/css/'.$args['name']);
    }

    public function elements($request, $response, $args)
    {
        $info = pathinfo($args['name']);
        $file_name =  basename($args['name'],'.'.$info['extension']);

        return $this->_container->view->render($response, 'staging/'.$file_name.'.phtml', [
            'args' => $args
        ]);
    }

    public function bundles($request, $response, $args)
    {
        return $response->withRedirect($this->_settings['params']['site_url'].'/themes/'.$this->_settings['params']['theme'].'/assets/build/bundles/'.$args['name']);
    }

    public function update($request, $response, $args)
    {
        /*if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }*/

        if (isset($args['name'])){
            $pos = strpos($args['name'], '.');
            if ($pos !== false) {
                return $this->_container->view->render($response, 'staging/'.$args['name'], [
                    'args' => $args
                ]);
            }

            $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

            $get_page = $tools->getPage($args['name'], false);
            if (!is_array($get_page))
                return false;

            $html_dom = new \PanelAdmin\Components\DomHelper();
            $html = $html_dom->str_get_html($get_page['content']);
            $sections = [];
            foreach ($html->find('section') as $section) {
                $sections[$section->id] = $section->innertext();
            }

            return $this->_container->view->render($response, 'staging/index.phtml', [
                'sections' => $sections,
                'page' => $args['name']
            ]);
            var_dump($sections); exit;

            /*$class_name = uniqid();
            $current_content = str_replace(array("[[", "]]"), array("{{", "}}"), $get_page['content']);

            $html = $html_dom->str_get_html('<div class="'.$class_name.'">'.$current_content.'</div>');*/

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
                    unlink($view_path.'/staging/'.$paths[1] . '.ehtml');
                }
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            return true;
        }
    }
}