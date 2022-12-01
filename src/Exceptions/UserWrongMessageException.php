<?php

namespace FinnAdvisor\Exceptions;

use RuntimeException;

class UserWrongMessageException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Ошибка! Пример использования команды: [+|-]сумма [описание]");
    }
}
