<?php

namespace FinnAdvisor\Tests\Integration\Service;

use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\Service\Categories\CategoriesRepository;
use FinnAdvisor\Service\NewMessageRouter;
use FinnAdvisor\Service\Operation\OperationRepository;
use FinnAdvisor\Service\UserResponseService;
use FinnAdvisor\VK\VKBotApiClient;
use PDOException;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /** @test */
    public function routerShouldCatchAllExceptions()
    {
        $operationRepository = $this->createStub(OperationRepository::class);
        $categoryRepository = $this->createStub(CategoriesRepository::class);
        $categoryRepository->method("getAllCategoriesForUser")
            ->willThrowException(new PDOException("test exception"));

        $apiClient = $this->createMock(VKBotApiClient::class);
        $apiClient->expects(self::once())
            ->method("sendMessage")
            ->with("Ой... Кажется, произошла какая-то ошибка... Уже разбираюсь!", 3);

        $responseService = new UserResponseService($categoryRepository, $operationRepository);
        $router = new NewMessageRouter($responseService, $apiClient);

        $router->processMessage(new NewMessage(1, 2, 3, "категории"));
    }
}