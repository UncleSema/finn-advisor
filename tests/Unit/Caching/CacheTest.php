<?php

namespace FinnAdvisor\Tests\Unit\Caching;

use FinnAdvisor\Caching\RedisClient;
use FinnAdvisor\Model\User;
use PHPUnit\Framework\TestCase;
use Predis\Client;

final class CacheTest extends TestCase
{
    /** @test */
    public function redisClientConstructorShouldNotThrowExceptionWhenOutage()
    {
        $this->notExistingRedisClient();
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     * @dataProvider redisClientMethodsToTest
     */
    public function clientMethodsShouldNotThrowExceptionsWhenOutage(string $methodName, mixed $parameter): void
    {
        $client = $this->notExistingRedisClient();
        $client->$methodName($parameter);
        $this->addToAssertionCount(1);
    }

    private function redisClientMethodsToTest(): array
    {
        return [
            ["getsetMessageId", ""],
            ["writeUser", new User("", "", "")],
            ["readUser", ""],
            ["disconnect", null]
        ];
    }

    private function notExistingRedisClient(): RedisClient
    {
        return new RedisClient(new Client("not_existing_host.com"));
    }
}
