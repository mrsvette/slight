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

    public function get_prices($data) {
        include_once __DIR__ . '/../../rank/components/simple_html_dom.php';

        $dmodel = new \ExtensionsModel\DomainResellerModel();
        $resellers = $dmodel->getRows(['enabled' => 1]);
        $items = [];
        foreach ($resellers as $i => $reseller) {
            $data['reseller'] = $reseller;
            $price_data = $this->{$reseller['code']}($data);
            $items[$reseller['code']] = [
                'reseller' => $reseller,
                'price' => $price_data['price'],
                'updated_at' => $price_data['updated_at'],
            ];
        }
        return $items;
    }

    private function nia($data) {
        $model = new \ExtensionsModel\DomainPriceModel();
        $domain_price = $model->get_price([
            'tld' => $data['tld'],
            'reseller_id' => $data['reseller']['id'],
            'hours_different' => 3
        ]);

        if ((int)$domain_price['price'] > 0) {
            return ['price' => (int)$domain_price['price'], 'updated_at' => $domain_price['updated_at']];
        }

        $postfields = [
            'sld' => $data['sld'],
            'tld' => $data['tld'],
            'transfer' => 0,
            'unicode_sld' => $data['sld']
        ];

        $url = $data['reseller']['url'];
        $url .= '?'.http_build_query($postfields);
        $html = file_get_html($url);
        $results = [];
        foreach ($html->find('option') as $e) {
            $val = $e->getAttribute('data-value');
            $needle = ["rp", ".", ",", " "];
            $replacements   = ["", "", ".", ""];

            $val = str_replace($needle, $replacements, strtolower($val));
            if ($e->getAttribute('value') == '1' || (int)$e->getAttribute('value') == 1) {
                $results[$data['tld']] = (int)$val;
                if ($results[$data['tld']] > 0) {
                    // saving the price
                    $save = $model->save_price([
                        'tld' => $data['tld'],
                        'reseller_id' => $data['reseller']['id'],
                        'price' => $results[$data['tld']]
                    ]);
                }
            }
        }

        return [ 'price' => $results[$data['tld']], 'updated_at' => date("Y-m-d H:i:s")];
    }

    /**
     * @param $data
     * @return array
     * result original format
     * array(13) {
     * ["reff"]=>"domain"
     * ["index"]=>int(1)
     * ["domain"]=>"gsahjdhsakas.co.id"
     * ["price"]=> "100000.00"
     * ["period"]=>"1y"
     * ["hostinghemat"]=>int(0)
     * ["description"]=> ""
     * ["type"]=>"register"
     * ["streakprice"]=>"0.00"
     * ["idp"]=>"0"
     * ["maxperiod"]=>"10"
     * ["promoperiod"]=>"0"
     * ["url"]=>s
     */
    private function rmw($data) {
        // check the data if any and still fresh
        $model = new \ExtensionsModel\DomainPriceModel();
        $domain_price = $model->get_price([
            'tld' => $data['tld'],
            'reseller_id' => $data['reseller']['id'],
            'hours_different' => 3
        ]);

        if ((int)$domain_price['price'] > 0) {
            return ['price' => (int)$domain_price['price'], 'updated_at' => $domain_price['updated_at']];
        }

        $url = $data['reseller']['url'];
        $curl_type = 'POST';
        $result_type = 'json';
        $postfields = [
            'registerdomain' => $data['sld'].$data['tld'],
            'promocode' => ''
        ];
        //create cURL connection
        $curl_connection = curl_init($url);

        //set options
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);

        if($curl_type == 'POST') {
            //set data to be posted
            curl_setopt($curl_connection, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl_connection, CURLOPT_POSTFIELDS, http_build_query($postfields));
        }else{
            curl_setopt($curl_connection, CURLOPT_URL, $url);
        }

        //perform our request
        $result = curl_exec($curl_connection);

        //close the connection
        curl_close($curl_connection);

        if($result_type == 'json'){
            $result = json_decode($result);
        }

        $results = $result->resultData->actionResult;
        $items = [];
        foreach ($results as $i => $result) {
            if (strpos($result, "hasilpencarian(") !== false) {
                $needle = ["hasilpencarian(", "})"];
                $replacements   = ["", "}"];

                $newphrase = str_replace($needle, $replacements, $result);
                $newphrase = json_decode($newphrase, true);;
                $period = $newphrase['period'];
                if ($period == 1 || strtolower($period) == '1y') {
                    $tld = $this->extract_tld($newphrase['domain']);
                    $items[$tld] = (int)$newphrase['price'];
                    if ($items[$tld] > 0) {
                        // saving the price
                        $save = $model->save_price([
                            'tld' => $tld,
                            'reseller_id' => $data['reseller']['id'],
                            'price' => $newphrase['price']
                        ]);
                    }
                }
            }
        }

        return [ 'price' => $items[$data['tld']], 'updated_at' => date("Y-m-d H:i:s")];
    }

    private function extract_tld( $domain ) {
        $productTLD = '';
        $tempstr = explode(".", $domain);
        unset($tempstr[0]);
        foreach($tempstr as $value){
            $productTLD = $productTLD.".".$value;
        }
        return $productTLD;
    }

    private function dms($data) {
        $model = new \ExtensionsModel\DomainPriceModel();
        $domain_price = $model->get_price([
            'tld' => $data['tld'],
            'reseller_id' => $data['reseller']['id'],
            'hours_different' => 3
        ]);

        if ((int)$domain_price['price'] > 0) {
            return ['price' => (int)$domain_price['price'], 'updated_at' => $domain_price['updated_at']];
        }

        $postfields = [
            'domain' => $data['sld'].$data['tld']
        ];

        $url = $data['reseller']['url'];
        $url .= '?'.http_build_query($postfields);
        $html = file_get_html($url);
        $tlds = [];
        foreach ($html->find('.popular-tld div') as $div) {
            $img_class = $div->getAttribute('class');
            if (!empty($img_class) && strpos($img_class, "-") !== false) {
                $exp = explode("-", $img_class);
                $tlds[] = '.'.$exp[0];
            }
        }

        $results = [];
        if (count($tlds) > 0 && $tlds[0] == $data['tld']) {
            $i = 0;
            foreach ($html->find('.current-price') as $e) {
                $val = $e->innertext;
                $needle = [".", ",", " "];
                $replacements   = ["", ".", ""];

                $val = str_replace($needle, $replacements, strtolower($val));
                if ((int)$val > 0) {
                    $results[$tlds[$i]] = (int)$val;
                    // saving the price
                    $save = $model->save_price([
                        'tld' => $tlds[$i],
                        'reseller_id' => $data['reseller']['id'],
                        'price' => (int)$val
                    ]);
                }
                $i ++;
            }

        } else {
            $price = $html->find('.current-price', 0)->innertext;
            if (!empty($price)) {
                $needle = [".", ",", " "];
                $replacements   = ["", ".", ""];

                $price = str_replace($needle, $replacements, strtolower($price));
                $results[$data['tld']] = (int)$price;
            }
        }

        return [ 'price' => $results[$data['tld']], 'updated_at' => date("Y-m-d H:i:s")];
    }
}