<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'monitoramento_ti';
    private $username = 'root';
    private $password = 'root';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log('Connection Error: ' . $e->getMessage());
            die('Erro ao conectar ao banco de dados. Tente novamente mais tarde.');
        }

        return $this->conn;
    }
}

// Cria a instância e a conexão global
$database = new Database();
$pdo = $database->connect();
?>