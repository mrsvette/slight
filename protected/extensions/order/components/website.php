<?php

namespace Extensions\Components;

class Website
{
    public $_order;
    public $_ext_order;

    public function __construct($order, $settings)
    {
        $this->_order = $order;
        $this->_settings = $settings;
        if (in_array('ext_order', array_keys($settings['params']))) {
            $ext_order = json_decode($settings['params']['ext_order'], true);
            $this->_ext_order = $ext_order;
        }
    }

    public function create()
    {
        $params = ['domain' => 'test.slightsite.com'];

        return $this->_request('v-list-web-domain', $params);
    }

    private function _request($command = 'v-list-web-domain', $params = null)
    {
        $postvars = array(
            'user' => $this->_ext_order['server_username'],
            'password' => $this->_ext_order['server_password'],
            'cmd' => $command,
            'arg3' => 'json'
        );

        switch ($command) {
            case 'v-list-web-domain':
                $postvars['arg1'] = $this->_ext_order['server_username'];
                $postvars['arg2'] = $params['domain'];
                break;
        }

        $postdata = http_build_query($postvars);

        // Send POST query via cURL
        $postdata = http_build_query($postvars);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->_ext_order['server_ip'] . ':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);

        // Parse JSON output
        $data = json_decode($answer, true);

        // Print result
        return $data;
    }
}