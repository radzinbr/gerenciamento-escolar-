
<?php

class Database
{
    private string $host = 'localhost';
    private string $database = 'sistema_escolar';
    private string $username = 'root';
    private string $password = '';

    public function connect(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

        try {
            return new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Não foi possível conectar ao banco de dados.',
                0,
                $e
            );
        }
    }
}
