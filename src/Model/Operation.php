<?php

namespace FinnAdvisor\Model;

final class Operation
{
    private string $id;
    private string $userId;
    private double $sum;
    private string $description;

    public function __construct($id, $userId, $sum, $description)
    {
        $this->id = $id;
        $this->sum = $sum;
        $this->userId = $userId;
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

    public function getDescription(): string
    {
        return $this->description;
    }
}
