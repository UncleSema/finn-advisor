<?php

namespace FinnAdvisor\Operation;

use FinnAdvisor\Config;
use Logger;
use PDO;
use PDOException;

class OperationRepository
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(Config $config)
    {
        $this->logger = Logger::getLogger("OperationRepository");

        $host = $config->getDatabaseHost();
        $dbname = $config->getDatabaseName();
        $user = $config->getDatabaseUser();
        $password = $config->getDatabasePassword();

        try {
            $this->pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname", $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            $this->logger->error("Unable to initialize OperationRepository", $e);
            die($e->getMessage());
        }
    }


}