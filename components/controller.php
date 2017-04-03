<?php

namespace Components;

class BaseController
{
    protected $_container;
    protected $_settings;
    protected $_user;

    public function __construct($app, $user)
    {
        $container = $app->getContainer();
        $this->_container = $container;
        $this->_settings = $container->get('settings');
        $this->_user = $user;

        $this->register($app);
    }
}