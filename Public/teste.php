<?php

require_once __DIR__ . '/../config/database.php';

$database = new Database();

try {

    $pdo = $database->connect();

    echo "Conexão com o banco realizada com sucesso!";

} catch (Exception $e) {

    echo "Erro: " . $e->getMessage();
}