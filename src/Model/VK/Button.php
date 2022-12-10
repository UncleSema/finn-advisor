<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class Button implements JsonSerializable
{
    private Action $action;
    private string $color;

    public function __construct(Action $action, string $color)
    {
        $this->action = $action;
        $this->color = $color;
    }

    public static function textButton(string $label, string $color): Button
    {
        return new Button(
            new Action(
                "text",
                "1",
                $label
            ),
            $color
        );
    }

    public function getAction(): Action
    {
        return $this->action;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
