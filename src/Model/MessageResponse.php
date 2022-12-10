<?php

namespace FinnAdvisor\Model;

use FinnAdvisor\Model\VK\Keyboard;
use FinnAdvisor\Model\VK\Template;

class MessageResponse
{
    private string $random_id;
    private string $peer_id;
    private string $message;
    private string $payload;
    private ?Template $template;
    private ?Keyboard $keyboard;

    public function __construct(
        string    $random_id,
        string    $peer_id,
        string    $message,
        string    $payload,
        ?Template $template,
        ?Keyboard $keyboard
    ) {
        $this->random_id = $random_id;
        $this->peer_id = $peer_id;
        $this->message = $message;
        $this->payload = $payload;
        $this->template = $template;
        $this->keyboard = $keyboard;
    }

    public function getRandomId(): string
    {
        return $this->random_id;
    }

    public function getPeerId(): string
    {
        return $this->peer_id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function getKeyboard(): ?Keyboard
    {
        return $this->keyboard;
    }

    public function getParams(): array
    {
        $params = [
            "random_id" => $this->random_id,
            "peer_id" => $this->peer_id,
            "message" => $this->message,
            "payload" => $this->payload,
        ];
        if ($this->template != null) {
            $params["template"] = json_encode($this->template);
        } elseif ($this->keyboard != null) {
            $params["keyboard"] = json_encode($this->keyboard);
        }
        return $params;
    }
}
