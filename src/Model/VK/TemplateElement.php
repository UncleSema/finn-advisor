<?php

namespace FinnAdvisor\Model\VK;

use JsonSerializable;

final class TemplateElement implements JsonSerializable
{
    private string $title;
    private string $description;
    private string $photo_id;
    private array $buttons;
    private TemplateAction $action;

    public function __construct(
        string $title,
        string $description,
        string $photo_id,
        array $buttons,
        TemplateAction $action
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->photo_id = $photo_id;
        $this->buttons = $buttons;
        $this->action = $action;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhotoId(): string
    {
        return $this->photo_id;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function getAction(): TemplateAction
    {
        return $this->action;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
