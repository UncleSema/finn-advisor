<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class Action implements JsonSerializable
{
    private string $type;
    private string $payload;
    private string $label;

    public function __construct(string $type, string $payload, string $label)
    {
        $this->type = $type;
        $this->payload = $payload;
        $this->label = $label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
