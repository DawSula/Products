<?php
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'user' =>  $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
];

