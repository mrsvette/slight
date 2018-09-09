<?php

namespace Extensions\Components;

class WebsiteTool
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

    public function check_availability($data) {
        if (isset($data['sld']) && isset($data['tld'])) {
            $tmodel = \ExtensionsModel\TldModel::model()->findByAttributes(['tld' => $data['tld']]);
            if ($tmodel instanceof \RedBeanPHP\OODBBean) {
                $rmodel = \ExtensionsModel\TldRegistrarModel::model()->findByPk($tmodel->registrar_id);
                $configs = json_decode($rmodel->configs, true);
                if (is_array($configs) && !empty($rmodel->url)) {
                    $data['sld'] = strtolower($data['sld']);
                    if (array_key_exists('domain', $configs['postfields'])) {
                        $configs['postfields']['domain'] = $data['sld'].$data['tld'];
                    }
                    if (array_key_exists('q', $configs['postfields'])) {
                        $configs['postfields']['q'] = $data['sld'].$data['tld'];
                    }
                    $url = $rmodel->url;
                    if ($configs['curl_type'] == 'GET') {
                        $url .= '?'.http_build_query($configs['postfields']);
                    }

                    //create cURL connection
                    $curl_connection = curl_init($url);

                    //set options
                    curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);

                    if($configs['curl_type'] == 'POST') {
                        //set data to be posted
                        curl_setopt($curl_connection, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, http_build_query($configs['postfields']));
                    }else{
                        curl_setopt($curl_connection, CURLOPT_URL, $url);
                    }

                    //perform our request
                    $result = curl_exec($curl_connection);
                    if($configs['result_type'] == 'json')
                        $result = json_decode($result);

                    //close the connection
                    curl_close($curl_connection);

                    if($configs['result_type'] == 'json'){
                        if($rmodel->id == 1) {
                            if (!is_object($result))
                                return false;
                            return $result->ExactMatchDomain->IsAvailable;
                        }
                    }

                    if(array_key_exists('regex', $configs)
                        && preg_match($configs['regex'], $result)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}