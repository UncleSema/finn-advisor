<?php

namespace FinnAdvisor\Tests\Unit\Service;

use FinnAdvisor\Model\NewMessage;
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
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testRoutesWithWhitespaces
     * @test
     */
    public function routerShouldIgnoreWhitespaces(string $testingText, string $method)
    {
        $responseServerMock = $this->createMock(UserResponseService::class);
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testRoutesUpperCase
     * @test
     */
    public function routerShouldIgnoreCase(string $testingText, string $method)
    {
        $responseServerMock = $this->createMock(UserResponseService::class);
        $responseServerMock->expects($this->once())
            ->method($method);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub);
        $router->processMessage($this->testMessage($testingText));
    }

    /**
     * @dataProvider testParsingArguments
     * @test
     */
    public function routerShouldParseRightArguments(string $testingText, string $method, mixed...$args)
    {

        $responseServerMock = $this->createMock(UserResponseService::class);
        $responseServerMock->expects($this->once())
            ->method($method)
            ->with(...$args);

        $apiClientStub = $this->createStub(VKBotApiClient::class);

        $router = new NewMessageRouter($responseServerMock, $apiClientStub);
        $router->processMessage($this->testMessage($testingText));
    }

    private function testRoutes(): array
    {
        return [
            ["помощь", "help"],
            ["+ категория", "addCategory"],
            ["- категория", "removeCategory"],
            ["категории", "allCategories"],
            ["+ 123 категория описание", "addOperation"],
            ["+ 123 категория", "addOperation"],
            ["убери", "removeOperation"],
            ["привет", "unknown"]
        ];
    }

    private function testRoutesWithWhitespaces(): array
    {
        return [
            ["\rпомощь\n", "help"],
            ["  + \n категория  ", "addCategory"],
            ["- \r категория ", "removeCategory"],
            ["  категории   ", "allCategories"],
            ["+    123 категория \rописание\n", "addOperation"],
            ["  + 123  категория   ", "addOperation"],
            ["  убери  ", "removeOperation"]
        ];
    }

    private function testRoutesUpperCase(): array
    {
        return [
            ["ПоМоЩь", "help"],
            ["+ КатегоРия", "addCategory"],
            ["- КатеГоРиЯ", "removeCategory"],
            ["КатеГорИИ", "allCategories"],
            ["+ 123 КаТегория ОпиСаНиЕ", "addOperation"],
            ["+ 123 КАТЕГОРИЯ", "addOperation"],
            ["Убери", "removeOperation"]
        ];
    }

    private function testParsingArguments(): array
    {
        return [
            [" ПоМоЩь ", "help"],
            [" + КатегоРия ", "addCategory", "3", "КатегоРия"],
            ["- КатеГоРиЯ", "removeCategory", "3", "КатеГоРиЯ"],
            ["КатеГорИИ", "allCategories", "3"],
            ["+ 123 КаТегория ОпиСаНиЕ", "addOperation", "3", 123, "КаТегория", "ОпиСаНиЕ"],
            ["+ 123 КАТЕГОРИЯ", "addOperation", "3", 123, "КАТЕГОРИЯ"],
            ["Убери", "removeOperation", "3"]
        ];
    }

    private function testMessage(string $text): NewMessage
    {
        return new NewMessage(1, 2, 3, $text);
    }
}
