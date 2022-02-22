<?php

declare(strict_types=1);

namespace App\src\Model\User;

use App\src\Model\Database;
use PDO;
use Throwable;
use App\src\Exception\StorageException;

class UserModel extends Database
{

    public function register(array $params)
    {
        try {
            $name = $this->conn->quote($params['name']);
            $password = $this->conn->quote($params['password']);
            $hashPass = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, password) VALUES ($name,'$hashPass')";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało zarejestrować', 400, $e);
        }

    }

    public function login(array $params)
    {
        try {
            $name = $this->conn->quote($params['name']);

            $query = "SELECT id FROM users WHERE name=$name";
            $result = $this->conn->query($query);

            $result = $result->fetch(PDO::FETCH_ASSOC);

            $_SESSION['id'] = $result['id'];
            $_SESSION['start'] = true;
        } catch (Throwable $e) {
            throw new StorageException('Nie udało zalogować', 400, $e);
        }


    }
}