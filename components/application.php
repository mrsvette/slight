<?php

namespace Components;

class Application
{
    public function getTheme()
    {
        if (!file_exists(realpath(dirname(__DIR__)).'/data/theme.json'))
            return 'default';

        $theme = file_get_contents(realpath(dirname(__DIR__)).'/data/theme.json');
        $theme = json_decode($theme, true);
        if (is_array($theme)){
            return $theme['id'];
        }

        return 'default';
    }
}