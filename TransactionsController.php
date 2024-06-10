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

    public function getTransactions()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM transactions");
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // header("Access-Control-Allow-Origin: *");
            $this->sendResponse(200, json_encode($transactions));
        } catch (PDOException $e) {
            $this->sendResponse(500, json_encode(['error' => 'Failed to retrieve transactions: ' . $e->getMessage()]));
        }
    }

    public function createTransaction($jsonData)
    {
        $item = json_decode($jsonData, true);

        //
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(400, json_encode(['error' => 'Invalid JSON data']));
            return;
        }
        //

        $dataCad = $item['data_cad'];
        $type = $item['type'];
        $description = $item['description'];
        $price = $item['price'];
        $category = $item['category'];
        $status = $item['status'];
        $account = $item['account'];

        //
        if (!$dataCad || !$type || !$description || !$price || !$category || !$status || !$account) {
            $this->sendResponse(400, json_encode(['error' => 'Missing required fields']));
            return;
        }
        //

        try {
            $stmt = $this->pdo->prepare("INSERT INTO transactions (data_cad, type, description, price, category, status, account) VALUES (:data_cad, :type, :description, :price, :category, :status, :account)");
            $stmt->bindParam(':data_cad', $dataCad);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':account', $account);
            $stmt->execute();
            $this->sendResponse(201, json_encode(['message' => 'Nova transação criada com sucesso!']));
            return;
        } catch (PDOException $e) {
            //
            error_log('Erro ao criar nova transação: ' . $e->getMessage());
            //
            $this->sendResponse(500, json_encode(['error' => 'Erro ao criar nova transação: ' . $e->getMessage()]));
        }

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

    public function sendResponse($status, $data)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo $data;
    }
}
