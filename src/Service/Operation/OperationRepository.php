<?php

namespace FinnAdvisor\Service\Operation;

use FinnAdvisor\Config;
use FinnAdvisor\Model\Operation;
use Logger;
use PDO;
use PDOException;

class OperationRepository
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function insertOperation(string $userId, float $sum, string $category, ?string $description): int
    {
        try {
            return $this->pdo
                ->query("INSERT INTO operations VALUES (DEFAULT, '$userId', $sum, '$category', '$description')")
                ->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == 23503) {
                return 0;
            }
            $this->logger->error("Exception during inserting operation ($userId, $sum, $category, $description)", $e);
            throw $e;
        }
    }

    public function deleteLastOperation(string $userId): ?Operation
    {
        try {
            $operations = $this->pdo
                ->query("DELETE FROM operations 
       WHERE id IN (SELECT MAX(id) FROM operations WHERE user_id='$userId') 
       RETURNING *")
                ->fetchAll();
            if (empty($operations)) {
                return null;
            }
            return new Operation(
                $operations[0]["id"],
                $operations[0]["user_id"],
                $operations[0]["sum"],
                $operations[0]["category"],
                $operations[0]["description"]
            );
        } catch (PDOException $e) {
            $this->logger->error("Exception during deleting last operation ($userId)", $e);
            throw $e;
        }
    }
}
