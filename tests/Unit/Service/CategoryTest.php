<?php

namespace FinnAdvisor\Tests\Unit\Service;

use FinnAdvisor\Service\Categories\CategoriesRepository;
use FinnAdvisor\Service\Operation\OperationRepository;
use FinnAdvisor\Service\UserResponseService;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    /** @test */
    public function allCategoriesShouldWarnIfNoCategories()
    {
        $service = $this->stubWithMethod("getAllCategoriesForUser", []);

        self::assertEquals(
            "У вас пока нет ни одной категории.",
            $service->allCategories("123")
        );
    }

    /** @test */
    public function allCategoriesShouldReturnMessageWithAllCategories()
    {
        $service = $this->stubWithMethod("getAllCategoriesForUser", ["cat1", "cat2", "cat3"]);

        self::assertEquals(
            <<<EOD
У вас есть следующие категории:
 - cat1
 - cat2
 - cat3
EOD,
            $service->allCategories("123")
        );
    }

    /** @test */
    public function addCategoryShouldWarnIfCategoryExists()
    {
        $service = $this->stubWithMethod("insertCategory", 0);
        self::assertEquals(
            "Хм, кажется, категория cat уже существует...",
            $service->addCategory("123", "cat")
        );
    }

    /** @test */
    public function addCategoryShouldInfoThatCategoryAdded()
    {
        $service = $this->stubWithMethod("insertCategory", 1);
        self::assertEquals(
            "Новая категория cat успешно добавлена!",
            $service->addCategory("123", "cat")
        );
    }

    /** @test */
    public function removeCategoryShouldWarnThatCategoryIsNotExisting()
    {
        $service = $this->stubWithMethod("deleteCategory", 0);
        self::assertEquals(
            "Хм, кажется, категории cat не существует...",
            $service->removeCategory("123", "cat")
        );
    }

    /** @test */
    public function removeCategoryShouldInfoThatCategoryRemoved()
    {
        $service = $this->stubWithMethod("deleteCategory", 1);
        self::assertEquals(
            "Категория cat успешно удалена!",
            $service->removeCategory("123", "cat")
        );
    }

    private function stubWithMethod(string $method, mixed $value): UserResponseService
    {
        $categoriesStub = $this->createStub(CategoriesRepository::class);
        $operationsStub = $this->createStub(OperationRepository::class);
        $categoriesStub->method($method)
            ->willReturn($value);

        return new UserResponseService($categoriesStub, $operationsStub);
    }
}
