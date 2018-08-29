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

    public function siteverify($secret, $response)
    {
        $data = array(
            'secret' => $secret,
            'response' => $response
        );

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($verify);
        $response = json_decode($response);

        return $response->success;
    }
}