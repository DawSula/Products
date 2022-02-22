<?php

declare(strict_types=1);

namespace App\src\Model\Product;

use App\src\Exception\StorageException;
use App\src\Model\Database;
use Throwable;
use PDO;

class CategoryModel extends Database
{

    public function list()
    {
        try {
            $query = "SELECT * FROM categories";
            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się pobrać kategorii', 400, $e);
        }
    }

    public function listId()
    {
        try {
            $query = "SELECT id FROM categories";
            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się pobrać kategorii', 400, $e);
        }
    }


}