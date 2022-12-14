<?php

namespace FinnAdvisor\Tests\Unit\Service;

use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\Service\Metrics\MetricsService;
use FinnAdvisor\Service\NewMessageRouter;
use FinnAdvisor\Service\UserResponseService;
use FinnAdvisor\VK\VKBotApiClient;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @dataProvider testRoutes
     * @test
     */
    public function routerShouldChooseRightRoutesDependingOnMessageContent(string $testingText, string $method): void
    {
        $responseServerMock = $this->createMock(UserResponseService::class);
        $metricsService = $this->createStub(MetricsService::class);
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub, $metricsService);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testRoutesWithWhitespaces
     * @test
     */
    public function routerShouldIgnoreWhitespaces(string $testingText, string $method)
    {
        $responseServerMock = $this->createMock(UserResponseService::class);
        $metricsService = $this->createStub(MetricsService::class);
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub, $metricsService);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testRoutesUpperCase
     * @test
     */
    public function routerShouldIgnoreCase(string $testingText, string $method)
    {
        $responseServerMock = $this->createMock(UserResponseService::class);
        $metricsService = $this->createStub(MetricsService::class);
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub, $metricsService);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testParsingArguments
     * @test
     */
    public function routerShouldParseRightArguments(string $testingText, string $method, mixed...$args)
    {

        $responseServerMock = $this->createMock(UserResponseService::class);
        $metricsService = $this->createStub(MetricsService::class);
        $responseServerMock->expects($this->once())
            ->method($method)
            ->with(...$args);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub, $metricsService);
        $router->processMessage($this->testMessage($testingText));
    }

    private function testRoutes(): array
    {
        return [
            ["????????????", "help"],
            ["+ ??????????????????", "addCategory"],
            ["- ??????????????????", "removeCategory"],
            ["??????????????????", "allCategories"],
            ["+ 123 ?????????????????? ????????????????", "addOperation"],
            ["+ 123 ??????????????????", "addOperation"],
            ["??????????", "removeOperation"],
            ["????????????", "unknown"]
        ];
    }

    private function testRoutesWithWhitespaces(): array
    {
        return [
            ["\r????????????\n", "help"],
            ["  + \n ??????????????????  ", "addCategory"],
            ["- \r ?????????????????? ", "removeCategory"],
            ["  ??????????????????   ", "allCategories"],
            ["+    123 ?????????????????? \r????????????????\n", "addOperation"],
            ["  + 123  ??????????????????   ", "addOperation"],
            ["  ??????????  ", "removeOperation"]
        ];
    }

    private function testRoutesUpperCase(): array
    {
        return [
            ["????????????", "help"],
            ["+ ??????????????????", "addCategory"],
            ["- ??????????????????", "removeCategory"],
            ["??????????????????", "allCategories"],
            ["+ 123 ?????????????????? ????????????????", "addOperation"],
            ["+ 123 ??????????????????", "addOperation"],
            ["??????????", "removeOperation"]
        ];
    }

    private function testParsingArguments(): array
    {
        return [
            [" ???????????? ", "help"],
            [" + ?????????????????? ", "addCategory", "3", "??????????????????"],
            ["- ??????????????????", "removeCategory", "3", "??????????????????"],
            ["??????????????????", "allCategories", "3"],
            ["+ 123 ?????????????????? ????????????????", "addOperation", "3", 123, "??????????????????", "????????????????"],
            ["+ 123 ??????????????????", "addOperation", "3", 123, "??????????????????"],
            ["??????????", "removeOperation", "3"]
        ];
    }

    private function testMessage(string $text): NewMessage
    {
        return new NewMessage(1, 2, 3, $text);
    }
}
