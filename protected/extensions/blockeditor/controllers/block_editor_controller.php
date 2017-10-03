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
            $sections = []; $elements = [];
            foreach ($html->find('section') as $section) {
                $s_content = $section->innertext();
                if (!empty($s_content)) {
                    $elements[$args['name'].'-'.$section->id] = array(
                        array(
                            'url' => 'elements/original/'.$args['name'].'-'.$section->id.'.ehtml',
                            'height' => 701,
                            'thumbnail' => 'elements/thumbs/basic.jpg'
                        )
                    );
                }
                $sections[$args['name'].'-'.$section->id] = $section->innertext();
            }
            // create the full page
            /*$full_elements = array(
                array(
                    'url' => 'elements/original/'.$args['name'].'.html',
                    'height' => 273,
                    'thumbnail' => 'elements/thumbs/basic.jpg'
                )
            );
            array_unshift($elements, $full_elements);*/

            try {
                $this->create_elements($elements);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            try {
                $this->create_section($sections);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            return $this->_container->view->render($response, 'staging/index.phtml', [
                'sections' => $sections,
                'page' => $args['name']
            ]);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function create_elements($data)
    {
        $elements_data = ['elements' => $data];
        $file_path = $this->_settings['theme']['path'] . '/' . $this->_settings['theme']['name'] . '/views/staging/';
        if (!file_exists($file_path.'elements.json')) {
            $fp = fopen($file_path.'elements.json', "wb");
            fwrite($fp, json_encode($elements_data));
            fclose($fp);
        } else {
            file_put_contents($file_path.'elements.json', json_encode($elements_data));
        }

        return true;
    }

    private function create_section($data)
    {
        $html_dom = new \PanelAdmin\Components\DomHelper();
        $format = new \PanelAdmin\Components\Format();
        
        $file_path = $this->_settings['theme']['path'] . '/' . $this->_settings['theme']['name'] . '/views/staging/original';
        $basic = file_get_contents($file_path.'/basic.html');

        foreach ($data as $section_name => $section_data) {
            if (!file_exists($file_path.'/'.$section_name.'.ehtml')) {
                $fp = fopen($file_path.'/'.$section_name.'.ehtml', "wb");
                fwrite($fp, $section_data);
                fclose($fp);
            }

            $html = $html_dom->str_get_html($basic);
            $html->find('.page', 0)->__set('innertext', '<div id="'.$section_name.'">'.$section_data.'</div>');

            $new_content = $html->find('html', 0)->innertext();
            $new_content = $format->HTML('<html lang="en">'.$new_content.'</html>');

            $update = file_put_contents($file_path.'/'.$section_name.'.ehtml', '<!DOCTYPE html>'.$new_content);
        }
        
        return true;
    }
}