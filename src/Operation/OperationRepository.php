<?php

namespace FinnAdvisor\Operation;

use FinnAdvisor\Config;
use Logger;
use PDO;
use PDOException;

class OperationRepository
{
    private PDO $pdo;

    public function __construct(Config $config)
    {
        $this->logger = Logger::getLogger("OperationRepository");


    }


}