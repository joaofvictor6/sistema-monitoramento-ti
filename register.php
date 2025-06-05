<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';

// Only allow admin users to register other admins
$allowAdminRegistration = isset($_SESSION['user_perfil']) && $_SESSION['user_perfil'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $perfil = $allowAdminRegistration ? sanitizeInput($_POST['perfil']) : 'visualizador';

    // Validações
    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um e-mail válido.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } elseif (strlen($password) < 8) {
        $error = 'A senha deve ter pelo menos 8 caracteres.';
    } else {
        try {
            $auth = new Auth();
            
            // Additional security: Only admin can register other admins
            if ($perfil === 'admin' && !$allowAdminRegistration) {
                $error = 'Apenas administradores podem criar contas de administrador.';
            } else {
                $result = $auth->register($nome, $email, $password, $perfil);
                
                if ($result['success']) {
                    $_SESSION['register_success'] = $result['message'];
                    redirect('login.php');
                } else {
                    $error = $result['message'];
                }
            }
        } catch(PDOException $e) {
            error_log('Registration Error: ' . $e->getMessage());
            $error = 'Erro durante o cadastro. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Cadastro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        .profile-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .profile-option {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
            background: #f8f9fa;
        }
        
        .profile-option:hover {
            border-color: #6e8efb;
        }
        
        .profile-option.selected {
            border-color: #6e8efb;
            background: rgba(110, 142, 251, 0.1);
        }
        
        .profile-option i {
            font-size: 24px;
            margin-bottom: 8px;
            display: block;
        }
        
        .profile-option input {
            display: none;
        }
        
        .admin-only {
            position: relative;
        }
        
        .admin-only::after {
            content: "Apenas para administradores";
            position: absolute;
            top: -10px;
            right: -10px;
            background: #f39c12;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container" style="max-width: 500px; margin: 0 auto;">
        <div class="login-card">
            <div class="login-header">
                <img src="assets/images/logo.png" alt="Logo Monitoramento TI" class="logo">
                <h1>Criar nova conta</h1>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome Completo</label>
                    <input type="text" id="nome" name="nome" required class="form-control" placeholder="Seu nome" value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <input type="email" id="email" name="email" required class="form-control" placeholder="seu@email.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                
                <?php if ($allowAdminRegistration): ?>
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Perfil do Usuário</label>
                    <div class="profile-selector">
                        <label class="profile-option <?php echo (!isset($perfil) || $perfil === 'visualizador') ? 'selected' : ''; ?>">
                            <input type="radio" name="perfil" value="visualizador" <?php echo (!isset($perfil) || $perfil === 'visualizador') ? 'checked' : ''; ?>>
                            <i class="fas fa-eye"></i>
                            <span>Visualizador</span>
                            <small class="text-muted d-block">Apenas visualização</small>
                        </label>
                        
                        <label class="profile-option admin-only <?php echo (isset($perfil) && $perfil === 'admin') ? 'selected' : ''; ?>">
                            <input type="radio" name="perfil" value="admin" <?php echo (isset($perfil) && $perfil === 'admin') ? 'checked' : ''; ?>>
                            <i class="fas fa-user-shield"></i>
                            <span>Administrador</span>
                            <small class="text-muted d-block">Acesso total</small>
                        </label>
                    </div>
                </div>
                <?php else: ?>
                    <input type="hidden" name="perfil" value="visualizador">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="password" name="password" required class="form-control" placeholder="•••••••• (mínimo 8 caracteres)">
                    <small class="form-text text-muted">A senha deve conter pelo menos 8 caracteres.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="form-control" placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-user-plus"></i> Cadastrar
                </button>
                
                <div class="login-footer">
                    <div class="register-link">
                        Já tem uma conta? <a href="login.php">Faça login</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para seleção visual dos perfis
        document.querySelectorAll('.profile-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.profile-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>