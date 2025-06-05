<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$db = (new Database())->connect();

if ($_SESSION['user_perfil'] === 'admin') {
    $query = "SELECT id, nome, email, perfil, ativo, 
              DATE_FORMAT(ultimo_login, '%d/%m/%Y %H:%i') as ultimo_login_formatado,
              DATE_FORMAT(criado_em, '%d/%m/%Y') as data_cadastro_formatado
              FROM usuarios ORDER BY nome";
} else {
    $query = "SELECT id, nome, email, perfil, 
              DATE_FORMAT(criado_em, '%d/%m/%Y') as data_cadastro_formatado
              FROM usuarios WHERE ativo = 1 ORDER BY nome";
}

$usuarios = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_perfil'] === 'admin') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['perfil'])) {
                        throw new Exception("Preencha todos os campos obrigatórios!");
                    }

                    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                    $stmt->execute([':email' => $_POST['email']]);
                    if ($stmt->rowCount() > 0) {
                        throw new Exception("Este e-mail já está cadastrado!");
                    }

                    $senhaTemporaria = bin2hex(random_bytes(4));
                    $senhaHash = password_hash($senhaTemporaria, PASSWORD_BCRYPT);

                    $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)");
                    $stmt->execute([
                        ':nome' => $_POST['nome'],
                        ':email' => $_POST['email'],
                        ':senha' => $senhaHash,
                        ':perfil' => $_POST['perfil']
                    ]);
                    
                    $_SESSION['message'] = [
                        'type' => 'success', 
                        'text' => 'Usuário cadastrado com sucesso! Senha temporária: ' . $senhaTemporaria
                    ];
                    break;
                    
                case 'edit':
                    $stmt = $db->prepare("UPDATE usuarios SET nome = :nome, perfil = :perfil, ativo = :ativo WHERE id = :id");
                    $ativo = isset($_POST['ativo']) ? 1 : 0;
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':nome' => $_POST['nome'],
                        ':perfil' => $_POST['perfil'],
                        ':ativo' => $ativo
                    ]);
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Usuário atualizado com sucesso!'];
                    break;
                    
                case 'reset-password':
                    $novaSenha = bin2hex(random_bytes(4));
                    $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':senha' => $senhaHash
                    ]);
                    $_SESSION['message'] = [
                        'type' => 'success', 
                        'text' => 'Senha resetada com sucesso! Nova senha: ' . $novaSenha
                    ];
                    break;
                    
                case 'delete':
                    if ($_POST['id'] == $_SESSION['user_id']) {
                        throw new Exception("Você não pode remover seu próprio usuário!");
                    }
                    $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
                    $stmt->execute([':id' => $_POST['id']]);
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Usuário removido com sucesso!'];
                    break;
            }
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            
        } catch(Exception $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => $e->getMessage()];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Gerenciar Usuários</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <style>
        /* ESTILOS APENAS PARA OS CARDS DE USUÁRIOS */
        .user-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .user-card .card-header {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            height: 80px;
            position: relative;
            border-bottom: none;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6e8efb;
            font-size: 2rem;
            margin: 0 auto;
            margin-top: -40px;
            border: 4px solid #6e8efb;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .user-status {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .status-active {
            background-color: #2ecc71;
        }
        
        .status-inactive {
            background-color: #e74c3c;
        }
        
        .user-role {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .role-admin {
            background-color: #f39c12;
            color: white;
        }
        
        .role-tecnico {
            background-color: #3498db;
            color: white;
        }
        
        .role-visualizador {
            background-color: #2ecc71;
            color: white;
        }
        
        .user-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
            background-color: rgba(255,255,255,0.9);
        }
        
        .user-card:hover .user-actions {
            opacity: 1;
        }
        
        /* ESTILOS PARA OS CARDS DE ESTATÍSTICAS */
        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto;
        }
        
        /* CLASSES UTILITÁRIAS */
        .non-admin .user-actions {
            display: none;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .breadcrumb .divider {
            color: #6c757d;
            padding: 0 5px;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="dashboard <?php echo ($_SESSION['user_perfil'] !== 'admin') ? 'non-admin' : ''; ?>">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><?php echo ($_SESSION['user_perfil'] === 'admin') ? 'Gerenciar' : 'Visualizar'; ?> Usuários</h1>
            <div class="breadcrumb">
                <span>Home</span>
                <span class="divider">/</span>
                <span class="active">Usuários</span>
            </div>
        </div>
        
        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show">
                    <?php echo $_SESSION['message']['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <?php if ($_SESSION['user_perfil'] === 'admin'): ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-white text-primary mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Total de Usuários</h5>
                            <p class="card-text display-6"><?php echo count($usuarios); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-white text-success mb-3">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5 class="card-title">Usuários Ativos</h5>
                            <p class="card-text display-6">
                                <?php echo count(array_filter($usuarios, function($u) { return isset($u['ativo']) ? $u['ativo'] == 1 : true; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-white text-info mb-3">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h5 class="card-title">Administradores</h5>
                            <p class="card-text display-6">
                                <?php echo count(array_filter($usuarios, function($u) { return $u['perfil'] == 'admin'; })); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Todos os Usuários</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Novo Usuário
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <?php foreach ($usuarios as $usuario): ?>
                <div class="col-md-4 mb-4">
                    <div class="card user-card h-100">
                        <div class="card-header position-relative">
                            <?php if (isset($usuario['ativo'])): ?>
                            <div class="user-status <?php echo $usuario['ativo'] ? 'status-active' : 'status-inactive'; ?>"></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body text-center pt-5">
                            <div class="user-avatar">
                                <?php
                                $nameParts = explode(' ', $usuario['nome']);
                                $initials = '';
                                foreach ($nameParts as $part) {
                                    if (!empty($part)) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                        if (strlen($initials) >= 2) break;
                                    }
                                }
                                echo $initials ?: '<i class="fas fa-user"></i>';
                                ?>
                            </div>
                            <h4 class="card-title mt-3 mb-1"><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($usuario['email']); ?></p>
                            
                            <span class="user-role role-<?php echo strtolower($usuario['perfil']); ?>">
                                <?php echo ucfirst($usuario['perfil']); ?>
                            </span>
                            
                            <div class="mt-3">
                                <?php if (isset($usuario['ultimo_login_formatado'])): ?>
                                <p class="mb-1"><small class="text-muted">Último login:</small></p>
                                <p class="mb-3"><?php echo $usuario['ultimo_login_formatado'] ? $usuario['ultimo_login_formatado'] : 'Nunca'; ?></p>
                                <?php endif; ?>
                                
                                <p class="mb-1"><small class="text-muted">Cadastrado em:</small></p>
                                <p><?php echo $usuario['data_cadastro_formatado']; ?></p>
                            </div>
                        </div>
                        <?php if ($_SESSION['user_perfil'] === 'admin'): ?>
                        <div class="card-footer bg-transparent user-actions">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                        data-id="<?php echo $usuario['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($usuario['nome']); ?>"
                                        data-email="<?php echo htmlspecialchars($usuario['email']); ?>"
                                        data-perfil="<?php echo $usuario['perfil']; ?>"
                                        data-ativo="<?php echo isset($usuario['ativo']) ? $usuario['ativo'] : '1'; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-outline-warning me-2 reset-btn"
                                        data-id="<?php echo $usuario['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($usuario['nome']); ?>">
                                    <i class="fas fa-key"></i>
                                </button>
                                
                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="<?php echo $usuario['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($usuario['nome']); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php if ($_SESSION['user_perfil'] === 'admin'): ?>
    <?php include __DIR__ . '/includes/user_modals.php'; ?>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($_SESSION['user_perfil'] === 'admin'): ?>
    <script src="assets/js/usuarios.js"></script>
    <?php endif; ?>
</body>
</html>