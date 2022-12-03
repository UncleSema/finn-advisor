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

    public function allCategories($userId): string
    {
        $categories = $this->categoriesRepository->getAllCategoriesForUser($userId);
        if (empty($categories)) {
            return "У вас пока нет ни одной категории.";
        }
        $joined = implode("\n - ", $categories);
        return "У вас есть следующие категории:\n - $joined";
    }

    public function help(): string
    {
        return <<<EOD
Ты можешь мне написать следующее:
1) +- сумма категория [описание] — добавить новую операцию 
2) +- категория — добавить новую категорию
3) категории — всё добавленные тобой категории
4) отчёт — полный отчёт о твоих операциях в pdf формате
5) отчёт категория — полный отчёт об операциях по заданной категории в pdf формате
6) помощь — я напишу это сообщение
7) напомни день +- сумма категория [описание] — я напомню в заданный день об операции
8) убери — я уберу последнюю операцию
9) убери [категория] — я уберу последнюю операцию заданной категории
EOD;
    }

    public function remove(): string
    {
        return "Убираю последнюю операциюю...";
    }

    public function statement(): string
    {
        return "Формирую отчёт...";
    }

    public function unknown(): string
    {
        return "Кажется, я не знаю такой команды... Напиши /помощь и я расскажу, что я умею!";
    }
}
