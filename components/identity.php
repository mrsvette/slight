<?php

namespace Components;

class UserIdentity
{
    protected $app;
    protected $container;
    protected $settings;
    protected $session_id;
    
    public function __construct($app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
        $this->settings = $this->container->get('settings');
        $this->session_id = md5($this->settings['name']);
    }
    
    public function isGuest()
    {
        if (!isset($_SESSION[$this->session_id])){
            return true;
        }

        return false;
    }
}