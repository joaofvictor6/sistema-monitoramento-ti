<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$db = (new Database())->connect();

// Buscar todos os dispositivos
$query = "SELECT * FROM dispositivos ORDER BY nome";
$dispositivos = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $db->prepare("INSERT INTO dispositivos 
                                        (nome, ip, tipo, status, local, observacoes, ultima_verificacao, usuario_id) 
                                        VALUES (:nome, :ip, :tipo, :status, :local, :observacoes, NOW(), :usuario_id)");
                    
                    $stmt->execute([
                        ':nome' => $_POST['nome'],
                        ':ip' => $_POST['ip'],
                        ':tipo' => $_POST['tipo'],
                        ':status' => $_POST['status'],
                        ':local' => $_POST['local'],
                        ':observacoes' => $_POST['observacoes'],
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Dispositivo cadastrado com sucesso!'];
                    break;
                    
                case 'edit':
                    $stmt = $db->prepare("UPDATE dispositivos SET 
                                        nome = :nome, 
                                        ip = :ip, 
                                        tipo = :tipo, 
                                        status = :status, 
                                        local = :local, 
                                        observacoes = :observacoes,
                                        ultima_verificacao = NOW(),
                                        usuario_id = :usuario_id
                                        WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':nome' => $_POST['nome'],
                        ':ip' => $_POST['ip'],
                        ':tipo' => $_POST['tipo'],
                        ':status' => $_POST['status'],
                        ':local' => $_POST['local'],
                        ':observacoes' => $_POST['observacoes'],
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Dispositivo atualizado com sucesso!'];
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM dispositivos WHERE id = :id");
                    $stmt->execute([':id' => $_POST['id']]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Dispositivo removido com sucesso!'];
                    break;
                    
                case 'verificar':
                    $stmt = $db->prepare("UPDATE dispositivos SET 
                                        ultima_verificacao = NOW(),
                                        status = :status,
                                        usuario_id = :usuario_id
                                        WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':status' => $_POST['status'],
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Status do dispositivo atualizado!'];
                    break;
            }
            
            // Redirecionar para evitar reenvio do formulário
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            
        } catch(PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro no banco de dados: ' . $e->getMessage()];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Dispositivos de Rede</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Dispositivos de Rede</h1>
            <div class="breadcrumb">
                <span>Home</span>
                <span class="divider">/</span>
                <span class="active">Dispositivos</span>
            </div>
        </div>
        
        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
                    <?php echo $_SESSION['message']['text']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Dispositivos na Rede (<?php echo count($dispositivos); ?>)</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                        <i class="fas fa-plus"></i> Adicionar Dispositivo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>IP</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Local</th>
                                    <th>Última Verificação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dispositivos as $dispositivo): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dispositivo['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($dispositivo['ip']); ?></td>
                                    <td><?php echo htmlspecialchars($dispositivo['tipo']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $dispositivo['status'] === 'Online' ? 'bg-success' : 
                                                  ($dispositivo['status'] === 'Manutenção' ? 'bg-warning' : 'bg-danger'); ?>">
                                            <?php echo $dispositivo['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($dispositivo['local']); ?></td>
                                    <td><?php echo $dispositivo['ultima_verificacao'] ? date('d/m/Y H:i', strtotime($dispositivo['ultima_verificacao'])) : 'Nunca'; ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-primary edit-btn" 
                                                    data-id="<?php echo $dispositivo['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($dispositivo['nome']); ?>"
                                                    data-ip="<?php echo htmlspecialchars($dispositivo['ip']); ?>"
                                                    data-tipo="<?php echo $dispositivo['tipo']; ?>"
                                                    data-status="<?php echo $dispositivo['status']; ?>"
                                                    data-local="<?php echo htmlspecialchars($dispositivo['local']); ?>"
                                                    data-observacoes="<?php echo htmlspecialchars($dispositivo['observacoes']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info verify-btn" 
                                                    data-id="<?php echo $dispositivo['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($dispositivo['nome']); ?>">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" 
                                                    data-id="<?php echo $dispositivo['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modais -->
    <?php include __DIR__ . '/includes/device_modals.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dispositivos.js"></script>
</body>
</html>