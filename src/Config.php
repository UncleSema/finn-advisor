<?php

namespace FinnAdvisor;

final class Config
{
    private string $token;
    private string $groupId;
    private string $databaseHost;
    private string $databaseName;
    private string $databaseUser;
    private string $databasePassword;
    private string $redisHost;
    private string $redisPassword;
    private string $redisDatabase;

    public function __construct(
        string $token = null,
        string $groupId = null,
        string $databaseHost = null,
        string $databaseName = null,
        string $databaseUser = null,
        string $databasePassword = null,
        string $redisHost = null,
        string $redisPassword = null,
        string $redisDatabase = null
    ) {
        $this->token = $this->getEnvIfNull($token, "VK_BOT_TOKEN");
        $this->groupId = $this->getEnvIfNull($groupId, "VK_GROUP_ID");
        $this->databaseHost = $this->getEnvIfNull($databaseHost, "DB_HOST");
        $this->databaseName = $this->getEnvIfNull($databaseName, "DB_NAME");
        $this->databaseUser = $this->getEnvIfNull($databaseUser, "DB_USER");
        $this->databasePassword = $this->getEnvIfNull($databasePassword, "DB_PASSWORD");
        $this->redisHost = $this->getEnvIfNull($redisHost, "REDIS_HOST");
        $this->redisPassword = $this->getEnvIfNull($redisPassword, "REDIS_PASSWORD");
        $this->redisDatabase = $this->getEnvIfNull($redisDatabase, "REDIS_DATABASE");
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function getDatabaseUser(): string
    {
        return $this->databaseUser;
    }

    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    public function getRedisHost(): string
    {
        return $this->redisHost;
    }

    public function getRedisPassword(): string
    {
        return $this->redisPassword;
    }

    public function getRedisDatabase(): string
    {
        return $this->redisDatabase;
    }

    private function getEnvIfNull($value, $env): string
    {
        if ($value == null) {
            return getenv($env);
        }
        return $value;
    }
}
