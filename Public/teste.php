
<?php

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();

    echo 'Conexão com o banco realizada com sucesso!';
} catch (RuntimeException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo 'Erro ao conectar ao banco. Verifique a configuração.';
}
