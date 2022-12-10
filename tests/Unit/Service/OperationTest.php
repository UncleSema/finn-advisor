<?php

namespace FinnAdvisor\Tests\Unit\Service;

use FinnAdvisor\Service\Categories\CategoriesRepository;
use FinnAdvisor\Service\Operation\OperationRepository;
use FinnAdvisor\Service\StatementService;
use FinnAdvisor\Service\UserResponseService;
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    /** @test */
    public function addOperationShouldWarnIfNoSuchCategory()
    {
        $service = $this->stubWithMethod("insertOperation", 0);
        self::assertEquals(
            "Не удалось добавить новую операцию... У вас точно есть категория cat?",
            $service->addOperation("1", 2, "cat", null)->getMessage()
        );
    }

    /** @test */
    public function removeOperationShouldWarnIfNoOperations()
    {
        $service = $this->stubWithMethod("deleteLastOperation", null);
        self::assertEquals(
            "Не удалось удалить последнюю операцию... Вы точно добавили хотя бы одну операцию?",
            $service->removeOperation("1")->getMessage()
        );
    }

    private function stubWithMethod(string $method, mixed $value): UserResponseService
    {
        $categoriesStub = $this->createStub(CategoriesRepository::class);
        $operationsStub = $this->createStub(OperationRepository::class);
        $statementService = $this->createStub(StatementService::class);
        $operationsStub->method($method)
            ->willReturn($value);

        return new UserResponseService($categoriesStub, $operationsStub, $statementService);
    }
}
