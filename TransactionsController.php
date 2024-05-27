<?php

class TransactionsController
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct($host, $dbname, $username, $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;

        $this->connectToDataBase();
    }

    private function connectToDatabase()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->sendResponse(500, json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            exit();
        }
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getTransactions();
                break;
            case 'POST':
                $this->createTransaction();
                break;
            case 'PUT':
                $this->updateTransaction();
                break;
            case 'DELETE':
                $this->deleteTransaction();
                break;
            default:
                $this->sendResponse(405, 'Method Not Allowed');
                break;
        }
    }

    public function getTransactions()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM transactions");
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            header("Access-Control-Allow-Origin: *");
            $this->sendResponse(200, json_encode($transactions));
        } catch (PDOException $e) {
            $this->sendResponse(500, json_encode(['error' => 'Failed to retrieve transactions: ' . $e->getMessage()]));
        }
    }

    private function createTransaction()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->sendResponse(201, 'Transaction Created');
    }

    private function updateTransaction()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->sendResponse(200, 'Transaction Updated');
    }

    private function deleteTransaction()
    {
        $this->sendResponse(200, 'Transaction Deleted');
    }

    private function sendResponse($status, $data)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo $data;
    }
}
