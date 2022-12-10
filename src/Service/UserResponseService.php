<?php

namespace FinnAdvisor\Service;

use DateTime;
use FinnAdvisor\Model\MessageResponse;
use FinnAdvisor\Model\Operation;
use FinnAdvisor\Model\VK\Button;
use FinnAdvisor\Model\VK\Keyboard;
use FinnAdvisor\Model\VK\Template;
use FinnAdvisor\Service\Categories\CategoriesRepository;
use FinnAdvisor\Service\Operation\OperationRepository;

class UserResponseService
{
    private CategoriesRepository $categoriesRepository;
    private OperationRepository $operationRepository;
    private StatementService $statementService;

    public function __construct(
        CategoriesRepository $categoriesRepository,
        OperationRepository  $operationRepository,
        StatementService     $statementService
    ) {
        $this->categoriesRepository = $categoriesRepository;
        $this->operationRepository = $operationRepository;
        $this->statementService = $statementService;
    }

    public function allCategories(string $userId): MessageResponse
    {
        $categories = $this->categoriesRepository->getAllCategoriesForUser($userId);
        if (empty($categories)) {
            $text = "У вас пока нет ни одной категории.";
        } else {
            $joined = implode("\n - ", $categories);
            $text = "У вас есть следующие категории:\n - $joined";
        }
        return $this->generateResponse($userId, $text);
    }

    public function addCategory(string $userId, string $category): MessageResponse
    {
        $added = $this->categoriesRepository->insertCategory($userId, $category);
        if ($added == 0) {
            $text = "Хм, кажется, категория $category уже существует...";
        } else {
            $text = "Новая категория $category успешно добавлена!";
        }
        return $this->generateResponse($userId, $text);
    }

    public function removeCategory(string $userId, string $category): MessageResponse
    {
        $deleted = $this->categoriesRepository->deleteCategory($userId, $category);
        if ($deleted == 0) {
            $text = "Хм, кажется, категории $category не существует...";
        } else {
            $text = "Категория $category успешно удалена!";
        }
        return $this->generateResponse($userId, $text);
    }

    public function help(string $userId): MessageResponse
    {
        $text = <<<EOD
Вот все команды, которые я знаю:
1) + сумма категория [дата] — добавить новую операцию (дата в формате дд-мм-гггг)
2) + категория — добавить новую категорию
3) - категория — удалить категорию
4) категории — всё добавленные категории
5) отчёт — полный отчёт об операциях
6) помощь — это сообщение
7) убери — убрать последнюю операцию
8) убери категория — убрать последнюю операцию заданной категории
9) операции — все операции
10) операции категория — все операции заданной категории
EOD;
        return $this->generateResponse($userId, $text);
    }

    public function addOperation(string $userId, string $sumRaw, string $category, ?string $date): MessageResponse
    {
        if ($date != null) {
            $date = $this->validateDate($date);
            if ($date == null) {
                return $this->generateResponse($userId, "Ошибка: дата должна быть в формате дд-мм-гггг");
            }
        }
        if (strlen($sumRaw) > 8) {
            return $this->generateResponse($userId, "Ошибка: слишком большая сумма $sumRaw");
        }
        $sum = (int) $sumRaw;
        $added = $this->operationRepository->insertOperation($userId, $sum, $category, $date);
        if ($added == 0) {
            $text = "Не удалось добавить новую операцию... У вас точно есть категория $category?";
        } else {
            $text = "Операция на сумму $sum в категории $category успешно добавлена!";
        }
        return $this->generateResponse($userId, $text);
    }

    public function removeOperation(string $userId): MessageResponse
    {
        $operation = $this->operationRepository->deleteLastOperation($userId);
        if ($operation == null) {
            $text = "Не удалось удалить последнюю операцию... Вы точно добавили хотя бы одну операцию?";
        } else {
            $sum = $operation->getSum();
            $category = $operation->getCategory();
            $text = "Операция на сумму $sum в категории $category успешно удалена!";
        }
        return $this->generateResponse($userId, $text);
    }

    public function removeOperationByCategory(string $userId, string $category): MessageResponse
    {
        $operation = $this->operationRepository->deleteLastOperationInCategory($userId, $category);
        if ($operation == null) {
            $text = "Не удалось удалить последнюю операцию... " .
                "Вы точно добавили хотя бы одну операцию для категории $category?";
        } else {
            $sum = $operation->getSum();
            $category = $operation->getCategory();
            $text = "Операция на сумму $sum в категории $category успешно удалена!";
        }
        return $this->generateResponse($userId, $text);
    }

    public function statement(string $userId): MessageResponse
    {
        $operations = $this->operationRepository->getAllOperations($userId);
        if (empty($operations)) {
            $statement = null;
            $text = "Вы не добавили ни одной операции :(";
        } else {
            $statement = $this->statementService->createStatement($userId, $operations);
            $text = "Формирую отчёт...";
        }
        return $this->generateResponse($userId, $text, $statement);
    }

    public function allOperations(string $userId): MessageResponse
    {
        $operations = $this->operationRepository->getAllOperations($userId);
        if (empty($operations)) {
            $text = "Вы не добавили ни одной операции :(";
        } else {
            $joined = $this->joinOperations($operations);
            $text = "У вас есть следующие операции:\n - $joined";
        }
        return $this->generateResponse($userId, $text);
    }

    public function operationsByCategory(string $userId, string $category): MessageResponse
    {
        $operations = $this->operationRepository->getOperationsByCategory($userId, $category);
        if (empty($operations)) {
            $text = "Вы не добавили ни одной операции для категории $category :(";
        } else {
            $joined = $this->joinOperations($operations);
            $text = "У вас есть следующие операции в категории $category:\n - $joined";
        }
        return $this->generateResponse($userId, $text);
    }

    private function joinOperations(array $operations): string
    {
        $mapped = array_map(
            fn(Operation $op): string => $op->getSum() . " " . $op->getCategory(),
            $operations
        );
        return implode("\n - ", $mapped);
    }

    public function serverError(string $userId): MessageResponse
    {
        return $this->generateResponse($userId, "Ой... Кажется, произошла какая-то ошибка... Уже разбираюсь!");
    }

    public function unknown(string $userId): MessageResponse
    {
        return $this->generateResponse(
            $userId,
            "Кажется, я не знаю такой команды... Напиши \"Помощь\" и я расскажу, что я умею!"
        );
    }

    private function validateDate(string $date): ?string
    {
        $format = "d-m-Y";
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $d->format("Y-m-d");
        }
        return null;
    }

    private function generateResponse(string $userId, string $text, Template $statement = null): MessageResponse
    {
        return new MessageResponse(
            rand(),
            $userId,
            $text,
            1000,
            $statement,
            new Keyboard(
                false,
                [
                    [Button::textButton("Отчёт", "primary")],
                    [
                        Button::textButton("Категории", "secondary"),
                        Button::textButton("Операции", "secondary")
                    ],
                    [Button::textButton("Помощь", "secondary")]
                ]
            )
        );
    }
}
