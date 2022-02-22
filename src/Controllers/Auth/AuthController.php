<?php

declare(strict_types=1);

namespace App\src\Controllers\Auth;

use App\src\Controllers\Controller;
use App\src\Model\User\UserModel;

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        $this->twig->addGlobal('_get', $_GET);
        echo $this->twig->render('Auth/login.html');
    }

    public function log()
    {
        $data = $this->request->getParams();
        if (!$this->validator->logValidate($data)) {
            self::redirect('/login', ['before' => 'logError']);
        }

        $this->userModel->login($data);
        self::redirect('/', ['before' => 'logSuccess']);
    }

    public function register()
    {
        $this->twig->addGlobal('_get', $_GET);
        echo $this->twig->render('Auth/register.html');
    }

    public function reg()
    {
        $data = $this->request->getParams();
        if (!($this->validator->regValidate($data))) {
            self::redirect('/register', ['before' => 'logError']);
        }
        $this->userModel->register($data);
        self::redirect('/login', ['before' => 'regSuccess']);
    }

    public function logout()
    {
        $logOut = $this->request->getParams();

        if ($logOut['session'] == 'unset') {
            session_unset();
        }
        self::redirect('/login', []);
    }
}