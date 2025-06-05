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
                    $ip = $db->query("SELECT ip FROM dispositivos WHERE id = " . (int)$_POST['id'])->fetchColumn();
                    $status = verificarStatusTCP($ip) ? 'Ativo' : 'Inativo';
                    
                    $stmt = $db->prepare("UPDATE dispositivos SET 
                                        ultima_verificacao = NOW(),
                                        status = :status,
                                        usuario_id = :usuario_id
                                        WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':status' => $status,
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Status do dispositivo atualizado para ' . $status . '!'];
                    break;
            }
            
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
                                            <?php echo $dispositivo['status'] === 'Ativo' ? 'bg-success' : 'bg-danger'; ?>">
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
    
    <!-- Modal Adicionar Dispositivo -->
    <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeviceModalLabel">Adicionar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Dispositivo</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ip" class="form-label">Endereço IP</label>
                            <input type="text" class="form-control" id="ip" name="ip" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="Servidor">Servidor</option>
                                <option value="Switch">Switch</option>
                                <option value="Roteador">Roteador</option>
                                <option value="Firewall">Firewall</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="local" class="form-label">Localização</label>
                            <input type="text" class="form-control" id="local" name="local" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Dispositivo -->
    <div class="modal fade" id="editDeviceModal" tabindex="-1" aria-labelledby="editDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeviceModalLabel">Editar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_nome" class="form-label">Nome do Dispositivo</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_ip" class="form-label">Endereço IP</label>
                            <input type="text" class="form-control" id="edit_ip" name="ip" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="edit_tipo" name="tipo" required>
                                <option value="Servidor">Servidor</option>
                                <option value="Switch">Switch</option>
                                <option value="Roteador">Roteador</option>
                                <option value="Firewall">Firewall</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_local" class="form-label">Localização</label>
                            <input type="text" class="form-control" id="edit_local" name="local" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Excluir Dispositivo -->
    <div class="modal fade" id="deleteDeviceModal" tabindex="-1" aria-labelledby="deleteDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDeviceModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir este dispositivo?</p>
                        <p class="fw-bold" id="delete_device_name"></p>
                        <p class="text-danger">Esta ação não pode ser desfeita!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Verificar Dispositivo -->
    <div class="modal fade" id="verifyDeviceModal" tabindex="-1" aria-labelledby="verifyDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyDeviceModalLabel">Verificar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="verificar">
                    <input type="hidden" name="id" id="verify_id">
                    <div class="modal-body">
                        <p>Deseja verificar o status atual do dispositivo?</p>
                        <p class="fw-bold" id="verify_device_name"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar modais de edição
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('editDeviceModal'));
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_nome').value = this.dataset.nome;
                document.getElementById('edit_ip').value = this.dataset.ip;
                document.getElementById('edit_tipo').value = this.dataset.tipo;
                document.getElementById('edit_status').value = this.dataset.status;
                document.getElementById('edit_local').value = this.dataset.local;
                document.getElementById('edit_observacoes').value = this.dataset.observacoes;
                modal.show();
            });
        });
        
        // Inicializar modais de exclusão
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('deleteDeviceModal'));
                document.getElementById('delete_id').value = this.dataset.id;
                document.getElementById('delete_device_name').textContent = 
                    this.parentNode.parentNode.parentNode.querySelector('td:first-child').textContent;
                modal.show();
            });
        });
        
        // Inicializar modais de verificação
        document.querySelectorAll('.verify-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('verifyDeviceModal'));
                document.getElementById('verify_id').value = this.dataset.id;
                document.getElementById('verify_device_name').textContent = this.dataset.nome;
                modal.show();
            });
        });
    </script>
</body>
</html>