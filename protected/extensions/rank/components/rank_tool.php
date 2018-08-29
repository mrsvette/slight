<?php

namespace Extensions\Components;

class RankTool
{
    protected $_basePath;

    public function __construct($_basePath = null)
    {
        $this->_basePath = $_basePath;
    }

    public function is_valid_domain_name($domain_name)
    {
        $parse = parse_url($domain_name);
        if (is_array($parse) && array_key_exists('host', $parse)) {
            $domain_name = $parse['host'];
        }
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}