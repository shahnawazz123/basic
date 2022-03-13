<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use yii\web\Session;
use yii\base\BootstrapInterface;

class Location implements BootstrapInterface
{

    //put your code here    
    public function bootstrap($app)
    {
        $session = new Session;
        $session->open();
        $session['timezone'] = 'Asia/Kuwait';
    }

    private function getJsonData($url, $params, $extern = "")
    {
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        if (count($params) != 0) {
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        //curl_setopt($ch,CURLOPT_URL, $url);
        //execute post
        $result = curl_exec($ch);

        //close connection        
        curl_close($ch);

        $json = json_decode($result, true);

        //debugPrint($result);exit;

        return $json;
    }

    private function getTimeZoneFromGoogle($cur_lat, $cur_long)
    {
        $key = 'key';
        $time = time();
        $url = 'https://maps.googleapis.com/maps/api/timezone/json?location=' . $cur_lat . ',' . $cur_long . '&key=' . $key . '&timestamp=' . $time;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array('Content-type:application/json'),
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($resp, true);

        return $result['timeZoneId'];
    }

}
