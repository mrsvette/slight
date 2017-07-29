<?php

namespace Components;

class BaseController
{
    protected $_container;
    protected $_settings;
    protected $_user;
    protected $_login_url = '/panel-admin/default/login';

    public function __construct($app, $user)
    {
        $container = $app->getContainer();
        $this->_container = $container;
        $this->_settings = $container->get('settings');
        $this->_user = $user;

        $this->register($app);
    }

    protected function isAllowed($request, $response)
    {
        $path = $request->getUri()->getPath();
        $action = end(explode('/',$path));

        $access_rules = $this->accessRules();
        $allows = [];
        if (is_array($access_rules)){
            foreach ($access_rules as $i => $rules) {
                if (in_array($action, $rules['actions']) && $rules[0] == 'allow'){
                    if (!empty($rules['users'][0])){
                        if ($rules['users'][0] == '@')
                            array_push($allows, !$this->_user->isGuest());
                    }
                    if (isset($rules['expression'])){
                        array_push($allows, $rules['expression']);
                    }
                }
                if ($rules[0] == 'deny'){
                    if (!empty($rules['users'][0])){
                        if ($rules['users'][0] == '*' && $this->_user->isGuest())
                            return $response->withRedirect($this->_login_url);
                    }
                }
            }
        }

        return !in_array(false, $allows);
    }

    public function notAllowedAction()
    {
        $this->_container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('You are not allowed to do this action!');
    }

    protected function hasAccess($path)
    {
        $model = new \Model\AdminGroupModel();
        return $model->hasAccess($this->_user, $path);
    }
}