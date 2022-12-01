<?php

namespace FinnAdvisor\Caching;

use Exception;
use FinnAdvisor\Config;
use FinnAdvisor\Model\User;
use Logger;
use Predis\Client;

class RedisClient
{
    private Client $redis;
    private Logger $logger;

    public function __construct(Config $config)
    {
        $this->logger = Logger::getLogger(__CLASS__);

        $host = $config->getRedisHost();
        $password = $config->getRedisPassword();
        $database = $config->getRedisDatabase();

        $this->redis = new Client("$host?password=$password&database=$database");
        try {
            $this->redis->connect();
        } catch (Exception $e) {
            $this->logger->error("Unable to connect to redis", $e);
        }
    }

    public function writeUser(User $user): void
    {
        try {
            if ($this->redis->isConnected()) {
                $this->redis->set($user->getId(), json_encode($user));
                $this->redis->expire($user->getId(), 60 * 60);
            }
        } catch (Exception $e) {
            $this->logger->error("Exception during writing user into cache", $e);
        }
    }

    public function readUser(string $id): User|null
    {
        try {
            if ($this->redis->isConnected()) {
                $cached = $this->redis->get($id);
                if ($cached == null) {
                    return null;
                }
                return User::jsonDeserialize($cached);
            }
        } catch (Exception $e) {
            $this->logger->error("Exception during reading user from cache", $e);
        }
        return null;
    }

    public function disconnect(): void
    {
        if ($this->redis->isConnected()) {
            $this->logger->debug("Disconnecting from redis...");
            try {
                $this->redis->disconnect();
            } catch (Exception $e) {
                $this->logger->error("Unable to disconnect from redis", $e);
            }
        }
    }
}
