<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

/**
 * Funções para o Dashboard
 */

function contarDispositivosRede($pdo) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return ['total' => 0, 'ativos' => 0];
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM dispositivos WHERE status = 'online'");
        $ativos = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM dispositivos");
        $total = $stmt->fetchColumn();
        
        return ['total' => $total, 'ativos' => $ativos];
    } catch (PDOException $e) {
        error_log("Erro ao contar dispositivos: " . $e->getMessage());
        return ['total' => 0, 'ativos' => 0];
    }
}

function contarImpressoras($pdo) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return ['total' => 0, 'ativas' => 0];
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM impressoras WHERE status = 'Ativo'");
        $ativas = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM impressoras");
        $total = $stmt->fetchColumn();
        
        return ['total' => $total, 'ativas' => $ativas];
    } catch (PDOException $e) {
        error_log("Erro ao contar impressoras: " . $e->getMessage());
        return ['total' => 0, 'ativas' => 0];
    }
}

function obterItensEstoque($pdo) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return [];
    }
    
    try {
        $stmt = $pdo->query("SELECT nome as item, local, quantidade FROM estoque ORDER BY quantidade ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao obter itens do estoque: " . $e->getMessage());
        return [];
    }
}

function contarItensEstoqueAtivos($pdo) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return 0;
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM estoque WHERE quantidade > 0");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erro ao contar itens ativos do estoque: " . $e->getMessage());
        return 0;
    }
}

function contarItensEstoqueCriticos($pdo) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return 0;
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM estoque WHERE quantidade < 3");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erro ao contar itens críticos do estoque: " . $e->getMessage());
        return 0;
    }
}

function obterLogsRecentes($pdo, $limite = 5) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível");
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT l.*, u.nome as usuario_nome 
            FROM logs l
            JOIN usuarios u ON l.usuario_id = u.id
            ORDER BY l.data_hora DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao obter logs recentes: " . $e->getMessage());
        return [];
    }
}

function formatarData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

function registrarLog($pdo, $usuario_id, $acao, $detalhes = null) {
    if (!$pdo) {
        error_log("Erro: Conexão com banco de dados não disponível para registrar log");
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO logs (usuario_id, acao, detalhes, data_registro) 
                               VALUES (:usuario_id, :acao, :detalhes, NOW())");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':acao' => $acao,
            ':detalhes' => $detalhes
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
        return false;
    }
}

/**
 * Funções adicionais úteis
 */

function getInitials($name) {
    $initials = '';
    $nameParts = explode(' ', $name);
    
    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) break;
        }
    }
    
    return $initials;
}

function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

function checkActivePage($pageName) {
    return (getCurrentPage() == $pageName) ? 'active' : '';
}
?>