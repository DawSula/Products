<?php

declare(strict_types=1);

namespace App\src\Model\Product;

use App\src\Model\Database;
use App\src\Exception\StorageException;
use App\src\Exception\NotFoundException;
use Throwable;
use PDO;

class ProductModel extends Database
{

    public function getAll()
    {
        try {
            $query = "SELECT products.title, products.id, products.status, products.description, categories.name FROM products LEFT JOIN categories ON products.category_id = categories.id";
            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            throw new StorageException('Nie udało się pobrać produktów', 400, $e);
        }
    }

    public function get(int $id): array
    {
        try {
            if ($this->checkCategory($id)) {
                $query = "SELECT products.title, products.id, category_id as productCategory, products.status, products.description, categories.name FROM products JOIN categories ON products.category_id = categories.id WHERE products.id = $id";
            } else {
                $query = "SELECT * FROM products WHERE products.id = $id";
            }

            $result = $this->conn->query($query);
            $product = $result->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się pobrać produktu', 400, $e);
        }
        if (!$product) {
            throw new NotFoundException("Produkt o id: $id nie istnieje");
        }

        return $product;
    }

    public function addProduct(array $params)
    {
        try {
            $quoted = $this->quoteParams($params);
            $query = "INSERT INTO products(title, description, status, category_id) VALUES({$quoted['name']},{$quoted['description']},{$quoted['status']},{$quoted['category']})";

            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się dodać produktu', 400, $e);
        }
    }

    public function deleteProduct(int $id)
    {
        try {
            $query = "DELETE FROM products WHERE id = $id";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się usunąć produktu', 400, $e);
        }
    }

    public function updateProduct($params)
    {
        try {
            $id = $this->conn->quote($params['productId']);
            $quoted = $this->quoteParams($params);
            $query = "UPDATE products 
                     SET title = {$quoted['name']}, description = {$quoted['description']}, status = {$quoted['status']}, category_id = {$quoted['category']}
                     WHERE id = $id";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się zaktualizować produktu', 400, $e);
        }
    }

    public function formatData(string $str)
    {
        $arr = trim($str);
        $arr = explode(" ", $arr);
        $newArr = [];
        foreach ($arr as $value) {
            if ($value != "") {
                $newArr[] = $value;
            }
        }
        $formated = implode(' ', $newArr);
        $formated = ucfirst($formated);

        return $formated;
    }

    private function checkCategory($id)
    {
        $query = "SELECT category_id FROM products where $id = id";
        $result = $this->conn->query($query);
        return $result->fetch(PDO::FETCH_ASSOC)['category_id'];
    }

    private function quoteParams(array $params)
    {
        $name = $this->formatData($params['name']);
        $name = $this->conn->quote($name);
        $description = $this->formatData($params['description']);
        $description = $this->conn->quote($description);
        $status = $this->conn->quote($params['status']);
        if (empty($params['category'])) {
            $category = "null";
        } else {
            $category = $this->conn->quote($params['category']);
        }
        return [
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'category' => $category];
    }


}