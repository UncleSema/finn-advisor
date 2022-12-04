<?php

use FinnAdvisor\Caching\RedisClient;
use FinnAdvisor\Config;
use FinnAdvisor\Service\Categories\CategoriesRepository;
use FinnAdvisor\Service\Operation\OperationRepository;
use FinnAdvisor\Service\NewMessageRouter;
use FinnAdvisor\Service\UserResponseService;
use FinnAdvisor\VK\VKBotApiClient;
use FinnAdvisor\VK\VKBotCallbackApiHandler;
use Predis\Client;
use VK\CallbackApi\LongPoll\VKCallbackApiLongPollExecutor;
use VK\Client\VKApiClient;

require_once "../vendor/autoload.php";

error_reporting(E_ALL ^ E_DEPRECATED);
Logger::configure("../resources/logback.xml");

$vk = new VKApiClient();
$config = new Config();
$host = $config->getDatabaseHost();
$dbname = $config->getDatabaseName();
$user = $config->getDatabaseUser();
$password = $config->getDatabasePassword();

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die($e->getMessage());
}

$categoriesRepository = new CategoriesRepository($pdo);
$operationRepository = new OperationRepository($pdo);

$redisHost = $config->getRedisHost();
$redis = new Client("$redisHost");
$redisClient = new RedisClient($redis);

$client = new VKBotApiClient($vk, $redisClient, $config);

$responseService = new UserResponseService($categoriesRepository, $operationRepository);
$messageRouter = new NewMessageRouter($responseService, $client);
$handler = new VKBotCallbackApiHandler($messageRouter, $redisClient);
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
