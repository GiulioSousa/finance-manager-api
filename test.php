<?php

require_once './TransactionsController.php';

$host = 'localhost:3306';
$dbname = 'finance_manager_db';
$username = 'root';
$password = 'Ebnx+=20019264';

$controller = new TransactionsController($host, $dbname, $username, $password);

$controller->getTransactions();
?>