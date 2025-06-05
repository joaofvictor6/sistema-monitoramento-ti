<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';
unset($_SESSION['register_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    
    $auth = new Auth();
    $result = $auth->login($email, $password);
    
    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['nome'];
        $_SESSION['user_email'] = $result['user']['email'];
        $_SESSION['user_perfil'] = $result['user']['perfil'];
        $_SESSION['last_login'] = $result['user']['ultimo_login'];
        
        $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'dashboard.php';
        unset($_SESSION['redirect_url']);
        redirect($redirect_url);
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        .login-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 500;
            margin-top: 10px;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container" style="max-width: 500px; margin: 0 auto;">
        <div class="login-card">
            <div class="login-header text-center">
                <img src="assets/images/logo.png" alt="Logo Monitoramento TI" class="logo">
                <h2 class="login-title">Gerenciador de Dispositivos DTI</h2>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <input type="email" id="email" name="email" required class="form-control" placeholder="seu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="password" name="password" required class="form-control" placeholder="••••••••">
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Lembrar-me</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
                
                <div class="login-footer">
                    <a href="#" class="forgot-password">Esqueceu sua senha?</a>
                    <div class="register-link">
                        Não tem uma conta? <a href="register.php">Cadastre-se</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
