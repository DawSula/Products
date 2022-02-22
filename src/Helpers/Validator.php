<?php

declare(strict_types=1);

namespace App\src\Helpers;

use App\src\Controllers\Controller;
use App\src\Model\Product\ProductModel;
use App\src\Model\Product\CategoryModel;
use App\src\Model\User\UserModel;
use PDO;

class Validator
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private UserModel $userModel;


    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
    }

    public function addProductValidate(array $params, string $path): bool
    {
        if (!$this->categoryValidate($params['category'])) {
            Controller::redirect($path, ['before' => 'categoryError']);
            return false;
        }
        if (empty($params['name'] || $params['description'])) {
            Controller::redirect($path, ['before' => 'emptyError']);
            return false;
        }
        $status = [0, 1];
        if (!in_array($params['status'], $status)) {
            Controller::redirect($path, ['before' => 'statusError']);
            return false;
        }
        if ($this->keysValidate($params)) {
            Controller::redirect($path, ['before' => 'keysError']);
            return false;
        }

        $name = $params['name'];
        $description = $params['description'];

        if (!$this->checkNameLen($name)) {
            Controller::redirect($path, ['before' => 'nameLengthError']);
            return false;
        }
        if (!$this->checkDescLen($description)) {
            Controller::redirect($path, ['before' => 'descLengthError']);
            return false;
        }

        return true;
    }

    public function logValidate(array $params): bool
    {
        foreach ($params as $value) {
            if ($value === "") {
                Controller::redirect('/login', ['before' => 'someEmpty']);
                return false;
            }
        }
        if (!($this->checkIfCorrect($params))) {
            Controller::redirect('/login', ['before' => 'incorrectData']);
            return false;
        }
        return true;
    }

    public function regValidate(array $params): bool
    {
        if (!($params['password'] === $params['repeatedPassword'])) {
            Controller::redirect('/register', ['before' => 'diffPass']);
            return false;
        }
        foreach ($params as $value) {
            if ($value === "") {
                Controller::redirect('/register', ['before' => 'someEmpty']);
                return false;
            }
        }
        if ($this->checkIfExist($params)) {
            Controller::redirect('/register', ['before' => 'userExist']);
            return false;
        }
        if (!($this->checkIfStrongPassword($params))) {
            Controller::redirect('/register', ['before' => 'weakPass']);
            return false;
        }
        return true;
    }

    private function checkIfCorrect(array $params): bool
    {
        $name = $this->userModel->conn->quote($params['name']);
        $password = $this->userModel->conn->quote($params['password']);

        $query = "SELECT * FROM users WHERE name=$name";
        $result = $this->userModel->conn->query($query);

        $result = $result->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return false;
        }
        if (!(password_verify($password, $result['password']))) {
            return false;
        } else {
            return true;
        }
    }

    private function checkIfStrongPassword(array $params): bool
    {
        $password = $params['password'];

        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!$uppercase || !$lowercase || !$number || strlen($password) < 6) {
            return false;
        } else {
            return true;
        }
    }

    private function checkIfExist(array $params): bool
    {
        $name = $this->userModel->conn->quote($params['name']);

        $query = "SELECT count(id) as names FROM users WHERE name=$name";
        $result = $this->userModel->conn->query($query);

        $result = $result->fetch(PDO::FETCH_ASSOC);

        if ((int)$result['names'] === 0) {
            return false;
        } else {
            return true;
        }
    }

    private function checkNameLen(string $name): bool
    {
        if (strlen($name) < 3 || strlen($name) > 30) {
            return false;
        }
        $name = $this->productModel->formatData($name);
        if (strlen($name) < 3 || strlen($name) > 30) {
            return false;
        }
        return true;
    }

    private function checkDescLen(string $description): bool
    {
        if (strlen($description) < 4 || strlen($description) > 350) {
            return false;
        }
        $description = $this->productModel->formatData($description);
        if (strlen($description) < 4 || strlen($description) > 350) {
            return false;
        }
        return true;
    }

    private function keysValidate(array $params): bool
    {
        return (preg_match('/[\'^*()}{@#~?><|=]/', $params['name'])) || (preg_match('/[\'^*()}{@#~?><|=]/', $params['description']));
    }

    private function categoryValidate($category): bool
    {
        $categoriesId = $this->categoryModel->listId();
        $categories = [];

        foreach ($categoriesId as $value) {
            foreach ($value as $val) {
                $categories[] = $val;
            }
        }
        return (in_array($category, $categories) || empty($category));
    }
}