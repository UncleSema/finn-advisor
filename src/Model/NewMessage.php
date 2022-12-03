<?php

namespace FinnAdvisor\Model;

class NewMessage
{
    private int $id;
    private int $date;
    private int $peerId;
    private string $text;

    public function __construct(int $id, int $date, int $peerId, string $text)
    {
        $this->id = $id;
        $this->date = $date;
        $this->peerId = $peerId;
        $this->text = $text;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getPeerId(): int
    {
        return $this->peerId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
