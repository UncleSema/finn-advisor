<?php

namespace FinnAdvisor\Model;

use JsonSerializable;

final class User implements JsonSerializable
{
    private string $id;
    private string $firstName;
    private string $lastName;

    public function __construct($id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public static function jsonDeserialize(string $json): User
    {
        $object = json_decode($json);
        return new User($object->{'id'}, $object->{'firstName'}, $object->{'lastName'});
    }
}
