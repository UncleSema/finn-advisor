<?php

namespace FinnAdvisor\Model;

final class Metric
{
    private string $type;
    private string $labels;
    private int $value;

    public function __construct(string $type, string $labels, int $value)
    {
        $this->type = $type;
        $this->labels = $labels;
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabels(): string
    {
        return $this->labels;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
