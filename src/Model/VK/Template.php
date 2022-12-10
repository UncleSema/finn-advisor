<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class Template implements JsonSerializable
{
    private string $type;
    private array $elements;

    public function __construct(string $type, array $elements)
    {
        $this->type = $type;
        $this->elements = $elements;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}