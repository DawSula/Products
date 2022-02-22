<?php

declare(strict_types=1);

namespace App\src\Controllers;

use App\src\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\src\Helpers\Validator;

class Controller
{

    protected $loader;
    protected $twig;
    protected Request $request;
    protected Validator $validator;

    public function __construct()
    {
        $this->loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($this->loader);
        $this->request = new Request();
        $this->validator = new Validator();
    }

    public static function redirect(string $to, array $params): void
    {
        $location = $to;

        if (count($params)) {
            $queryParams = [];
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }
            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }
        header("Location: $location");
        exit;
    }


}