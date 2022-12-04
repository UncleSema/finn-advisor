<?php

namespace FinnAdvisor\Model;

final class Operation
{
    private int $id;
    private string $userId;
    private int $sum;
    private string $category;
    private ?string $description;

    public function __construct(int $id, string $userId, int $sum, string $category, ?string $description)
    {
        $this->id = $id;
        $this->sum = $sum;
        $this->userId = $userId;
        $this->category = $category;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
