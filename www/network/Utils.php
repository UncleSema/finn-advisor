<?php

class Utils {
    public static function curl_get($url, $params) {

        $url = $url . "?" . http_build_query($params);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    public static function vk_request($method, $params) {
        $params["access_token"] = Config::TOKEN;
        $params["v"] = Config::API;

        $res = self::curl_get("https://api.vk.com/method/$method", $params);

        if (!isset($res["response"])) {
            print($res["error"]["error_msg"] . PHP_EOL);
            return false;
        }

        return $res["response"];
    }
}
