<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['senha'])) {
                    // Atualizar último login
                    $this->updateLastLogin($user['id']);
                    
                    return [
                        'success' => true,
                        'user' => [
                            'id' => $user['id'],
                            'nome' => $user['nome'],
                            'email' => $user['email'],
                            'perfil' => $user['perfil'],
                            'ultimo_login' => $user['ultimo_login']
                        ]
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Credenciais inválidas'];
        } catch(PDOException $e) {
            error_log('Login Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro durante o login. Tente novamente.'];
        }
    }

    public function register($nome, $email, $password, $perfil = 'visualizador') {
        try {
            // Verificar se o e-mail já existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Este e-mail já está em uso.'];
            }

            // Criar hash da senha
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Inserir novo usuário
            $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $password_hash);
            $stmt->bindParam(':perfil', $perfil);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Cadastro realizado com sucesso! Você já pode fazer login.'];
            } else {
                return ['success' => false, 'message' => 'Erro ao cadastrar usuário. Tente novamente.'];
            }
        } catch(PDOException $e) {
            error_log('Registration Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro durante o cadastro. Tente novamente.'];
        }
    }

    private function updateLastLogin($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log('Update Last Login Error: ' . $e->getMessage());
        }
    }
}
?>