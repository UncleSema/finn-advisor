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

    public function insertOperation(string $userId, float $sum, string $category, ?string $date): int
    {
        try {
            $date = ($date == null ? "DEFAULT" : "'$date'");
            return $this->pdo
                ->query(
                    "INSERT INTO operations VALUES (DEFAULT, '$userId', $sum, '$category', $date)"
                )
                ->rowCount();
        } catch (PDOException $e) {
            $this->logger->error("Exception during inserting operation ($userId, $sum, $category, $date)", $e);
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
            return $this->parseOperation($operations[0]);
        } catch (PDOException $e) {
            $this->logger->error("Exception during deleting last operation ($userId)", $e);
            throw $e;
        }
    }

    public function deleteLastOperationInCategory(string $userId, string $category): ?Operation
    {
        try {
            $operations = $this->pdo
                ->query("DELETE FROM operations 
       WHERE id IN (SELECT MAX(id) FROM operations WHERE user_id='$userId' and category='$category') 
       RETURNING *")
                ->fetchAll();
            if (empty($operations)) {
                return null;
            }
            return $this->parseOperation($operations[0]);
        } catch (PDOException $e) {
            $this->logger->error("Exception during deleting last operation for category ($userId, $category)", $e);
            throw $e;
        }
    }

    public function getOperationsByCategory(string $userId, string $category): array
    {
        try {
            $rows = $this->pdo
                ->query("SELECT * FROM operations WHERE user_id='$userId' and category='$category'")
                ->fetchAll();
            $operations = [];
            foreach ($rows as $row) {
                $operations[] = $this->parseOperation($row);
            }
            return $operations;
        } catch (PDOException $e) {
            $this->logger->error("Exception during getting operations by category ($userId, $category)", $e);
            throw $e;
        }
    }

    public function getAllOperations(string $userId): array
    {
        try {
            $rows = $this->pdo
                ->query("SELECT * FROM operations WHERE user_id='$userId'")
                ->fetchAll();
            $operations = [];
            foreach ($rows as $row) {
                $operations[] = $this->parseOperation($row);
            }
            return $operations;
        } catch (PDOException $e) {
            $this->logger->error("Exception during getting all operations ($userId)", $e);
            throw $e;
        }
    }

    public function parseOperation(array $row): Operation
    {
        return new Operation(
            $row["id"],
            $row["user_id"],
            $row["sum"],
            $row["category"],
            $row["date"]
        );
    }
}
