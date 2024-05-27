<?php

require_once './env.php';
require_once './TransactionsController.php';

loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

$controller = new TransactionsController($host, $dbname, $username, $password);

$routes = [
    '/transactions' => 'TransactionsController'
];

$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestUri) {
    case '/transactions':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->getTransactions();
        } else {
            $controller->sendResponse(405, json_encode(['error' => 'Method Not Allowed']));
        }
        break;

    default:
        $controller->sendResponse(404, json_encode(['error' => 'Not Found']));
        break;
}