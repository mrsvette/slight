<?php

namespace Components;

class Application
{
    public function getThemeConfig()
    {
        $hash = md5(__CLASS__.'/themes/');
        $config = substr($hash, 0, 10);

        return $config;
    }

    public function getTheme()
    {
        if (!file_exists(realpath(dirname(__DIR__)).'/data/'.self::getThemeConfig().'.th'))
            return 'default';

        $theme = file_get_contents(realpath(dirname(__DIR__)).'/data/'.self::getThemeConfig().'.th');
        $theme = json_decode($theme, true);
        if (is_array($theme)){
            return $theme['id'];
        }

        return 'default';
    }
}