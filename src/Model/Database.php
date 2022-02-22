<?php

declare(strict_types=1);

namespace App\src\Model;

use PDO;
use PDOException;
use App\src\Exception\StorageException;
use App\src\Exception\ConfigurationException;

class Database
{
    private static array $config;

    public PDO $conn;

    public function __construct()
    {
        try {
            $this->validateConfig(self::$config);
            $this->createConnection(self::$config);
        } catch (PDOException $e) {
            throw new StorageException('Connection error');
        }
    }

    private function createConnection(array $config): void
    {
        $dsn = "mysql:dbname={$config['database']};host={$config['host']}";
        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    public static function setConfig($config): void
    {
        self::$config = $config;
    }

    private function validateConfig(array $config): void
    {
        if (
            empty($config['database'])
            || empty($config['host'])
            || empty($config['user'])
        ) {
            throw new ConfigurationException('Storage configuration error');
        }
    }
}