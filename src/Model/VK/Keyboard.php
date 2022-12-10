<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class Keyboard implements JsonSerializable
{
    private bool $one_time;
    private array $buttons;

    public function __construct(bool $one_time, array $buttons)
    {
        $this->one_time = $one_time;
        $this->buttons = $buttons;
    }

    public function isOneTime(): bool
    {
        return $this->one_time;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
