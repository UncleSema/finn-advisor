<?php
function vkAPI($method, $params = []) {
    $params["v"] = Config::API;
    $params["access_token"] = Config::getToken();

    $url = "https://api.vk.com/method/" . $method . "?";
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    ));
    $json = curl_exec($myCurl);
    curl_close($myCurl);

    $result = json_decode($json, true);

    if (isset($result["error"])) {
        return $result;
    }
    return $result["response"];
}

function message_send($message, $peer_id, $attachments = []) {
    return vkAPI('messages.send', [
        'random_id' => rand(),
        'peer_id' => $peer_id,
        'message' => $message,
        'payload' => 1000,
        'attachment' => implode(',', $attachments)
    ]);
}