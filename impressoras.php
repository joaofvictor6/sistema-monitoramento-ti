<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$db = (new Database())->connect();

// Buscar todas as impressoras
$query = "SELECT * FROM impressoras ORDER BY nome";
$impressoras = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $db->prepare("INSERT INTO impressoras 
                                        (nome, ip, mac, serial, status, local, usuario_id) 
                                        VALUES (:nome, :ip, :mac, :serial, :status, :local, :usuario_id)");
                    
                    $stmt->execute([
                        ':nome' => $_POST['nome'],
                        ':ip' => $_POST['ip'],
                        ':mac' => $_POST['mac'],
                        ':serial' => $_POST['serial'],
                        ':status' => $_POST['status'],
                        ':local' => $_POST['local'],
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Impressora cadastrada com sucesso!'];
                    break;
                    
                case 'edit':
                    $stmt = $db->prepare("UPDATE impressoras SET 
                                        nome = :nome, 
                                        ip = :ip, 
                                        mac = :mac, 
                                        serial = :serial, 
                                        status = :status, 
                                        local = :local,
                                        usuario_id = :usuario_id
                                        WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':nome' => $_POST['nome'],
                        ':ip' => $_POST['ip'],
                        ':mac' => $_POST['mac'],
                        ':serial' => $_POST['serial'],
                        ':status' => $_POST['status'],
                        ':local' => $_POST['local'],
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Impressora atualizada com sucesso!'];
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM impressoras WHERE id = :id");
                    $stmt->execute([':id' => $_POST['id']]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Impressora removida com sucesso!'];
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
    <title>Monitoramento de TI - Impressoras</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Monitoramento de Impressoras</h1>
            <div class="breadcrumb">
                <span>Home</span>
                <span class="divider">/</span>
                <span class="active">Impressoras</span>
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
                    <h3>Impressoras na Rede (<?php echo count($impressoras); ?>)</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrinterModal">
                        <i class="fas fa-plus"></i> Adicionar Impressora
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome/Modelo</th>
                                    <th>IP</th>
                                    <th>MAC</th>
                                    <th>Serial</th>
                                    <th>Status</th>
                                    <th>Local</th>
                                    <th>Última Atualização</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($impressoras as $impressora): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($impressora['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($impressora['ip']); ?></td>
                                    <td><?php echo htmlspecialchars($impressora['mac']); ?></td>
                                    <td><?php echo htmlspecialchars($impressora['serial']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $impressora['status'] === 'ativo' ? 'bg-success' : 
                                                  ($impressora['status'] === 'manutencao' ? 'bg-warning' : 'bg-danger'); ?>">
                                            <?php echo ucfirst($impressora['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($impressora['local']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($impressora['data_atualizacao'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-btn" 
                                                data-id="<?php echo $impressora['id']; ?>"
                                                data-nome="<?php echo htmlspecialchars($impressora['nome']); ?>"
                                                data-ip="<?php echo htmlspecialchars($impressora['ip']); ?>"
                                                data-mac="<?php echo htmlspecialchars($impressora['mac']); ?>"
                                                data-serial="<?php echo htmlspecialchars($impressora['serial']); ?>"
                                                data-status="<?php echo $impressora['status']; ?>"
                                                data-local="<?php echo htmlspecialchars($impressora['local']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" 
                                                data-id="<?php echo $impressora['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
    
    <!-- Modais (mantidos iguais ao código anterior) -->
    <?php include __DIR__ . '/includes/printer_modals.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/impressoras.js"></script>
</body>
</html>