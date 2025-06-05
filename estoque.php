<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$db = (new Database())->connect();

// Buscar todos os itens de estoque
$query = "SELECT * FROM estoque ORDER BY tipo, nome";
$itensEstoque = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais por tipo
$totaisPorTipo = [];
foreach ($itensEstoque as $item) {
    if (!isset($totaisPorTipo[$item['tipo']])) {
        $totaisPorTipo[$item['tipo']] = 0;
    }
    $totaisPorTipo[$item['tipo']] += $item['quantidade'];
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $status = calcularStatus($_POST['quantidade']);
                    
                    $stmt = $db->prepare("INSERT INTO estoque 
                                        (nome, tipo, quantidade, local, status, usuario_id) 
                                        VALUES (:nome, :tipo, :quantidade, :local, :status, :usuario_id)");
                    
                    $stmt->execute([
                        ':nome' => $_POST['nome'],
                        ':tipo' => $_POST['tipo'],
                        ':quantidade' => $_POST['quantidade'],
                        ':local' => $_POST['local'],
                        ':status' => $status,
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Item cadastrado com sucesso!'];
                    break;
                    
                case 'edit':
                    $status = calcularStatus($_POST['quantidade']);
                    
                    $stmt = $db->prepare("UPDATE estoque SET 
                                        nome = :nome, 
                                        tipo = :tipo, 
                                        quantidade = :quantidade, 
                                        local = :local, 
                                        status = :status,
                                        usuario_id = :usuario_id
                                        WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $_POST['id'],
                        ':nome' => $_POST['nome'],
                        ':tipo' => $_POST['tipo'],
                        ':quantidade' => $_POST['quantidade'],
                        ':local' => $_POST['local'],
                        ':status' => $status,
                        ':usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Item atualizado com sucesso!'];
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM estoque WHERE id = :id");
                    $stmt->execute([':id' => $_POST['id']]);
                    
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Item removido com sucesso!'];
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

function calcularStatus($quantidade) {
    if ($quantidade == 0) {
        return 'esgotado';
    } elseif ($quantidade < 3) {
        return 'baixo';
    } else {
        return 'disponivel';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Controle de Estoque</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Controle de Estoque</h1>
            <div class="breadcrumb">
                <span>Home</span>
                <span class="divider">/</span>
                <span class="active">Estoque</span>
            </div>
        </div>
        
        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
                    <?php echo $_SESSION['message']['text']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <!-- Cards de totais por tipo -->
            <div class="row mb-4">
                <?php foreach ($totaisPorTipo as $tipo => $total): ?>
                <div class="col-md-3">
                    <div class="card summary-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($tipo); ?></h5>
                            <p class="card-text display-6"><?php echo $total; ?> itens</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Inventário de Estoque</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Atualizado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itensEstoque as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tipo']); ?></td>
                                    <td><?php echo $item['quantidade']; ?></td>
                                    <td><?php echo htmlspecialchars($item['local']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $item['status'] === 'disponivel' ? 'bg-success' : 
                                                  ($item['status'] === 'baixo' ? 'bg-warning' : 
                                                  ($item['status'] === 'esgotado' ? 'bg-danger' : 'bg-info')); ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['data_atualizacao'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-btn" 
                                                data-id="<?php echo $item['id']; ?>"
                                                data-nome="<?php echo htmlspecialchars($item['nome']); ?>"
                                                data-tipo="<?php echo htmlspecialchars($item['tipo']); ?>"
                                                data-quantidade="<?php echo $item['quantidade']; ?>"
                                                data-local="<?php echo htmlspecialchars($item['local']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" 
                                                data-id="<?php echo $item['id']; ?>">
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
    
    <!-- Modais -->
    <?php include __DIR__ . '/includes/estoque_modals.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/estoque.js"></script>
</body>
</html>