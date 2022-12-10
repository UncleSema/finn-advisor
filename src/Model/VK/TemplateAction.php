<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class TemplateAction implements JsonSerializable
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
