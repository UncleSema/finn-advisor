<?php

namespace FinnAdvisor\Model;

final class Operation
{
    private int $id;
    private string $userId;
    private int $sum;
    private string $category;
    private string $date;

    public function __construct(int $id, string $userId, int $sum, string $category, string $date)
    {
        $this->id = $id;
        $this->sum = $sum;
        $this->userId = $userId;
        $this->category = $category;
        $this->date = $date;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSum(): int
    {
        return $this->sum;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}
