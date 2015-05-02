<?php

class HttpUtils
{

    public static function returnJson($object, $httpStatusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($httpStatusCode);
        echo json_encode($object);
        exit();
    }

    public static function ping($url) {
        $results = array();

        if(substr($url,0,4)!="http") $url = "http://".$url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_exec($ch);

        if(!curl_errno($ch)) {
            $results['bytes'] = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            $results['total_time'] = floor(curl_getinfo($ch, CURLINFO_TOTAL_TIME)*1000);
        } else {
            return false;
        }
        curl_close($ch);

        return $results;
    }

    public static function sendSms($phone, $text) {
        $curl = curl_init();
        $text = curl_escape($curl, $text);
        $senderHost = sprintf(Config::PORTAL_SENDER_URL,
            Config::PORTAL_SENDER_HOST,
            Config::PORTAL_SENDER_HOST_PORT,
            Config::PORTAL_SENDER_HOST_PWD,
            $phone, $text);
        curl_setopt($curl, CURLOPT_URL, $senderHost);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
        $err = curl_errno($curl) ? curl_error($curl) : null;
        curl_close($curl);

        return $err;
    }

}