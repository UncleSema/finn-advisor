<?php

namespace FinnAdvisor\VK;

use Exception;
use FinnAdvisor\Caching\RedisClient;
use FinnAdvisor\Config;
use FinnAdvisor\Model\MessageResponse;
use FinnAdvisor\Model\User;
use Logger;
use Throwable;
use VK\Client\VKApiClient;

class VKBotApiClient
{
    private VKApiClient $client;
    private RedisClient $cache;
    private Logger $logger;
    private Config $config;

    public function __construct(VKApiClient $client, RedisClient $cache, Config $config)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->config = $config;

        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function sendMessage(MessageResponse $response): void
    {
        try {
            $this->client->messages()->send($this->config->getToken(), $response->getParams());
        } catch (Throwable $e) {
            $this->logger->error("Error during sending message\n$e");
        }
    }

    public function uploadPhotos(string $path): string
    {
        try {
            $address = $this->client->photos()->getMessagesUploadServer($this->config->getToken());
            $photo = $this->client->getRequest()->upload($address['upload_url'], 'photo', $path);
            $response_save_photo = $this->client->photos()->saveMessagesPhoto($this->config->getToken(), [
                'server' => $photo['server'],
                'photo' => $photo['photo'],
                'hash' => $photo['hash'],
            ])[0];
            return $response_save_photo["owner_id"] . "_" . $response_save_photo["id"];
        } catch (Exception $e) {
            $this->logger->error("Unexpected exception during uploading photo", $e);
        }
        return "";
    }

    public function getUser(string $peerId): User
    {
        $cachedUser = $this->cache->readUser($peerId);
        if ($cachedUser != null) {
            $this->logger->debug("User $peerId was found in cache");
            return $cachedUser;
        }
        $this->logger->debug("User $peerId wasn't found in cache: making an api request");
        try {
            $response = $this->client->users()->get($this->config->getToken(), [
                'user_ids' => [$peerId],
                'fields' => ['id', 'first_name', 'second_name']
            ])[0];
            $user = new User($response["id"], $response["first_name"], $response["last_name"]);
            $this->cache->writeUser($user);
            return $user;
        } catch (Exception $e) {
            $this->logger->error("Exception during getting user for id $peerId", $e);
            return new User($peerId, "", "");
        }
    }
}
