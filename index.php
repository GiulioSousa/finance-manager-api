<?php

require_once './env.php';
require_once './TransactionsController.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
// header("Access-Control-Allow-Origin: http://localhost:3001");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

$controller = new TransactionsController($host, $dbname, $username, $password);

$routes = [
    '/transactions' => 'TransactionsController',
    '/create-transactions' => 'TransactionsController'
];

$requestUri = $_SERVER['REQUEST_URI'];
// $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($requestUri) {
    case '/transactions':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->getTransactions();
        } else {
            $controller->sendResponse(405, json_encode(['error' => 'Method Not Allowed']));
        }
        break;

    case '/create-transaction':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jsonData = file_get_contents('php://input');
            $controller->createTransaction($jsonData);
        } else {
            $controller->sendResponse(405, json_encode(['error' => 'Method Not Allowed']));
        }
        break;
    case '/update-transaction':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $jsonData = file_get_contents('php://input');
            $controller->updateTransaction($jsonData);
        } else {
            $controller->sendResponse(405, json_encode(['error' => 'Method Not Allowed']));
        }
        break;

    default:
        $controller->sendResponse(404, json_encode(['error' => 'Not Found']));
        break;
}
