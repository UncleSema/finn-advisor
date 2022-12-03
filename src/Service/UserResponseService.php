<?php

namespace FinnAdvisor\Service;

use FinnAdvisor\Categories\CategoriesRepository;

class UserResponseService
{
    private CategoriesRepository $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    public function allCategories(string $userId): string
    {
        $categories = $this->categoriesRepository->getAllCategoriesForUser($userId);
        if (empty($categories)) {
            return "У вас пока нет ни одной категории.";
        }
        $joined = implode("\n - ", $categories);
        return "У вас есть следующие категории:\n - $joined";
    }

    public function addCategory(string $userId, string $category): string
    {
        $added = $this->categoriesRepository->insertCategory($userId, $category);
        if ($added == 0) {
            return "Хм, кажется, категория $category уже существует...";
        }
        return "Новая категория $category успешно добавлена!";
    }

    public function removeCategory(string $userId, string $category): string
    {
        $deleted = $this->categoriesRepository->deleteCategory($userId, $category);
        if ($deleted == 0) {
            return "Хм, кажется, категории $category не существует...";
        }
        return "Категория $category успешно удалена!";
    }

    public function help(): string
    {
        return <<<EOD
Ты можешь мне написать следующее:
1) + сумма категория [описание] — добавить новую операцию 
2) + категория — добавить новую категорию
3) - категория — удалить категорию
4) категории — всё добавленные тобой категории
5) отчёт — полный отчёт о твоих операциях в pdf формате
6) отчёт категория — полный отчёт об операциях по заданной категории в pdf формате
7) помощь — я напишу это сообщение
8) напомни день +- сумма категория [описание] — я напомню в заданный день об операции
9) убери — я уберу последнюю операцию
10) убери [категория] — я уберу последнюю операцию заданной категории
EOD;
    }

    public function remove(): string
    {
        return "Убираю последнюю операцию...";
    }

    public function statement(): string
    {
        return "Формирую отчёт...";
    }

    public function serverError(): string
    {
        return "Ой... Кажется, произошла какая-то ошибка... Уже разбираюсь!";
    }

    public function unknown(): string
    {
        return "Кажется, я не знаю такой команды... Напиши \"помощь\" и я расскажу, что я умею!";
    }
}
