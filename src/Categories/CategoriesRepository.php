<?php

namespace FinnAdvisor\Categories;

use Logger;
use PDO;
use PDOException;

class CategoriesRepository
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function getAllCategoriesForUser(string $userId): array
    {
        $rows = $this->pdo
            ->query("SELECT category FROM categories WHERE user_id = '$userId'")
            ->fetchAll();
        $categories = array();
        foreach ($rows as $row) {
            $categories[] = $row["category"];
        }
        return $categories;
    }

    public function insertCategory(string $userId, string $category): int
    {
        try {
            return $this->pdo
                ->query("INSERT INTO categories VALUES ('$userId', '$category')")
                ->rowCount();
        } catch (PDOException $e) {
            // value exists
            if ($e->getCode() == 23505) {
                return 0;
            }
            throw $e;
        }
    }

    public function deleteCategory(string $userId, string $category): int
    {
        return $this->pdo
            ->query("DELETE FROM categories WHERE user_id='$userId' AND category='$category'")
            ->rowCount();
    }

//    public function existsCategory(string $userId, string $category): bool
//    {
//        return $this->pdo
//            ->query("SELECT category FROM categories WHERE user_id='$userId' and category=")
//    }
}
