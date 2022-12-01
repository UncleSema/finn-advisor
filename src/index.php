<?php

use FinnAdvisor\Caching\RedisClient;
use FinnAdvisor\Config;
use FinnAdvisor\VK\VKBotApiClient;
use FinnAdvisor\VK\VKBotCallbackApiHandler;
use VK\CallbackApi\LongPoll\VKCallbackApiLongPollExecutor;
use VK\Client\VKApiClient;

require_once "../vendor/autoload.php";

error_reporting(E_ALL ^ E_DEPRECATED);
Logger::configure("../resources/logback.xml");

$vk = new VKApiClient();
$config = new Config();
$redisClient = new RedisClient($config);

$client = new VKBotApiClient($vk, $redisClient, $config);
$handler = new VKBotCallbackApiHandler($client);
$executor = new VKCallbackApiLongPollExecutor($vk, $config->getToken(), $config->getGroupId(), $handler, 25);

try {
    while (true) {
        try {
            $executor->listen();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
} finally {
    $redisClient->disconnect();
}
