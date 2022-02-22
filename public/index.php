<?php

require __DIR__ . '\..\vendor\autoload.php';

use App\src\Controllers\Products\ProductController;
use App\src\Model\Database;
use App\src\Controllers\Controller;
use App\src\Controllers\Auth\AuthController;

$config = require_once __DIR__ . "\..\config\config.php";

session_start();

Database::setConfig($config);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ProductController::class . '/list');
    $r->addRoute('GET', '/addProduct', ProductController::class . '/add');
    $r->addRoute('GET', '/{id:\d+}', ProductController::class . '/show');
    $r->addRoute('GET', '/edit/{id:\d+}', ProductController::class . '/edit');
    $r->addRoute('POST', '/DeleteProduct', ProductController::class . '/delete');
    $r->addRoute('POST', '/update', ProductController::class . '/update');
    $r->addRoute('POST', '/create', ProductController::class . '/create');

    $r->addRoute('GET', '/login', AuthController::class . '/login');
    $r->addRoute('GET', '/register', AuthController::class . '/register');
    $r->addRoute('POST', '/log', AuthController::class . '/log');
    $r->addRoute('POST', '/reg', AuthController::class . '/reg');
    $r->addRoute('POST', '/logout', AuthController::class . '/logout');

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        Controller::redirect('/', ['before' => 'urlError']);
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        die('Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("/", $handler, 2);
        call_user_func_array(array(new $class, $method), $vars);
        break;
}