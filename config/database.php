<?php

class Database
{
    private string $host = '';
    private string $database = '';
    private string $username = '';
    private string $password = '';

    public function connect(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

        try {

            $pdo = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $pdo;

        } catch (PDOException $e) {

            die(
                "Erro ao conectar ao banco: " .
                $e->getMessage()
            );
        }
    }
}