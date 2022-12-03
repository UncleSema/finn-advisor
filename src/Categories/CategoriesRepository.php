<?php

namespace FinnAdvisor\Categories;

use PDO;

class CategoriesRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
}
