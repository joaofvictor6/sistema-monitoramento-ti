<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF']);
$initials = getInitials($_SESSION['user_name']);

// Dados para o dashboard com verificação TCP
$dispositivos = contarDispositivosRede($pdo);
$impressoras = contarImpressoras($pdo);

$estoque = obterItensEstoque($pdo);
$itens_ativos = contarItensEstoqueAtivos($pdo);
$itens_criticos = contarItensEstoqueCriticos($pdo);

// Calcular porcentagens
$percent_rede = $dispositivos['total'] > 0 ? round(($dispositivos['ativos']/$dispositivos['total'])*100) : 0;
$percent_imp = $impressoras['total'] > 0 ? round(($impressoras['ativas']/$impressoras['total'])*100) : 0;

// Registrar acesso
registrarLog($pdo, $_SESSION['user_id'], 'Acesso ao Dashboard');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de TI - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --secondary: #3f37c9;
            --success: #4cc9a0;
            --warning: #f8961e;
            --danger: #f94144;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --sidebar: #1e293b;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            font-size: 0.9rem;
        }
        
        /* Top Navigation */
        .top-navbar {
            background-color: var(--sidebar);
            height: 60px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-container {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .brand {
            display: flex;
            align-items: center;
            color: white;
            font-weight: 600;
        }
        
        .brand-logo {
            height: 26px;
            margin-right: 12px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0 0 0 2rem;
            padding: 0;
        }
        
        .nav-item {
            margin: 0 0.3rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 0.6rem 0.8rem;
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .nav-link i {
            margin-right: 6px;
            font-size: 0.9rem;
        }
        
        .nav-link:hover, .nav-item.active .nav-link {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .user-menu {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            font-weight: bold;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            color: rgba(255,255,255,0.8);
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 0.85rem;
        }
        
        .logout-btn i {
            margin-right: 5px;
        }
        
        /* Main Content */
        .main-content {
            margin-top: 60px;
            padding: 1.5rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.3rem;
            font-size: 1.4rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 1.1rem;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-value {
            font-weight: 700;
            font-size: 1.3rem;
            line-height: 1.2;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--card-shadow);
        }
        
        .chart-title {
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: var(--dark);
        }
        
        .chart-container {
            position: relative;
            height: 180px;
        }
        
        /* Progress Bars */
        .progress-bars {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .progress-card {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--card-shadow);
        }
        
        .progress-title {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .progress-percent {
            font-weight: 600;
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
            background-color: #e2e8f0;
        }
        
        /* Inventory Table */
        .inventory-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .card-header {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .card-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inventory-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 0.6rem 1rem;
            text-align: left;
            font-size: 0.8rem;
        }
        
        .inventory-table td {
            padding: 0.6rem 1rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .progress-bars {
                grid-template-columns: 1fr;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                margin-right: 0;
            }
            
            .user-name {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="navbar-container">
            <div class="brand">
                <img src="assets/images/icone.png" alt="Logo" class="brand-logo">
                <span>Monitoramento de TI</span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item <?= $current_page == 'impressoras.php' ? 'active' : '' ?>">
                    <a href="impressoras.php" class="nav-link"><i class="fas fa-print"></i> <span>Impressoras</span></a>
                </li>
                <li class="nav-item <?= $current_page == 'dispositivos.php' ? 'active' : '' ?>">
                    <a href="dispositivos.php" class="nav-link"><i class="fas fa-network-wired"></i> <span>Dispositivos</span></a>
                </li>
                <li class="nav-item <?= $current_page == 'estoque.php' ? 'active' : '' ?>">
                    <a href="estoque.php" class="nav-link"><i class="fas fa-box-open"></i> <span>Estoque</span></a>
                </li>
                <li class="nav-item <?= $current_page == 'usuarios.php' ? 'active' : '' ?>">
                    <a href="usuarios.php" class="nav-link"><i class="fas fa-users-cog"></i> <span>Usuários</span></a>
                </li>
            </ul>
            
            <div class="user-menu">
                <div class="user-avatar"><?= $initials ?></div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i> <span>Sair</span>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <small class="text-muted">Status atualizado em: <?= $dispositivos['atualizado'] ?></small>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-server"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $dispositivos['total'] ?></div>
                    <div class="stat-label">Dispositivos de Rede</div>
                    <small class="text-muted"><?= $dispositivos['ativos'] ?> ativos</small>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-secondary">
                    <i class="fas fa-print"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $impressoras['total'] ?></div>
                    <div class="stat-label">Impressoras</div>
                    <small class="text-muted"><?= $impressoras['ativas'] ?> ativas</small>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $itens_ativos ?></div>
                    <div class="stat-label">Itens em Estoque</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $itens_criticos ?></div>
                    <div class="stat-label">Itens Críticos</div>
                </div>
            </div>
        </div>
        
        <!-- Progress Bars -->
        <div class="progress-bars">
            <div class="progress-card">
                <div class="progress-title">
                    <span>Dispositivos Ativos</span>
                    <span class="progress-percent"><?= $percent_rede ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent_rede ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted"><?= $dispositivos['ativos'] ?> ativos</small>
                    <small class="text-muted"><?= $dispositivos['total'] ?> total</small>
                </div>
            </div>
            
            <div class="progress-card">
                <div class="progress-title">
                    <span>Impressoras Ativas</span>
                    <span class="progress-percent"><?= $percent_imp ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= $percent_imp ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted"><?= $impressoras['ativas'] ?> ativas</small>
                    <small class="text-muted"><?= $impressoras['total'] ?> total</small>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-card">
                <h3 class="chart-title">Status dos Dispositivos</h3>
                <div class="chart-container">
                    <canvas id="networkChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h3 class="chart-title">Status das Impressoras</h3>
                <div class="chart-container">
                    <canvas id="printersChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Inventory Table -->
        <div class="inventory-card">
            <div class="card-header">
                <h3 class="card-title">Estoque de TI</h3>
            </div>
            <div class="table-responsive">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Localização</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estoque)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">Nenhum item encontrado no estoque</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estoque as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item']) ?></td>
                                <td><?= htmlspecialchars($item['local']) ?></td>
                                <td><?= $item['quantidade'] ?></td>
                                <td>
                                    <span class="status-badge 
                                        <?= $item['quantidade'] == 0 ? 'bg-danger' : 
                                           ($item['quantidade'] < 3 ? 'bg-warning' : 'bg-success') ?>">
                                        <?= $item['quantidade'] == 0 ? 'Esgotado' : 
                                           ($item['quantidade'] < 3 ? 'Baixo' : 'OK') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh
        setTimeout(() => location.reload(), 300000);
        
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Network Devices Chart
            const netCtx = document.getElementById('networkChart').getContext('2d');
            new Chart(netCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ativos', 'Inativos'],
                    datasets: [{
                        data: [<?= $dispositivos['ativos'] ?>, <?= $dispositivos['total'] - $dispositivos['ativos'] ?>],
                        backgroundColor: ['#4cc9a0', '#e2e8f0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
            
            // Printers Chart
            const printCtx = document.getElementById('printersChart').getContext('2d');
            new Chart(printCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ativas', 'Inativas'],
                    datasets: [{
                        data: [<?= $impressoras['ativas'] ?>, <?= $impressoras['total'] - $impressoras['ativas'] ?>],
                        backgroundColor: ['#3f37c9', '#e2e8f0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });

        // Atualização automática via AJAX
        function atualizarStatus() {
            fetch('api/status.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta');
                    return response.json();
                })
                .then(data => {
                    if (!data.error) {
                        // Atualizar dispositivos
                        document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = data.dispositivos.total;
                        document.querySelector('.stat-card:nth-child(1) small:nth-child(3)').textContent = `${data.dispositivos.ativos} ativos`;
                        
                        // Atualizar impressoras
                        document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.impressoras.total;
                        document.querySelector('.stat-card:nth-child(2) small:nth-child(3)').textContent = `${data.impressoras.ativas} ativas`;
                        
                        // Atualizar timestamp
                        document.querySelector('.page-header small').textContent = `Status atualizado em: ${data.atualizado}`;
                        
                        // Atualizar gráficos
                        updateChart('networkChart', data.dispositivos.ativos, data.dispositivos.total - data.dispositivos.ativos);
                        updateChart('printersChart', data.impressoras.ativas, data.impressoras.total - data.impressoras.ativas);
                    }
                })
                .catch(error => console.error('Erro ao atualizar status:', error));
        }

        // Função para atualizar os gráficos
        function updateChart(chartId, active, inactive) {
            const chart = Chart.getChart(chartId);
            if (chart) {
                chart.data.datasets[0].data = [active, inactive];
                chart.update();
            }
        }

        // Atualizar a cada 30 segundos
        setInterval(atualizarStatus, 30000);

        // Atualizar imediatamente ao carregar (opcional)
        // atualizarStatus();
    </script>
</body>
</html>