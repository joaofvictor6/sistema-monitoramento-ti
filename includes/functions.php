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
 * Funções para verificação de status via TCP
 */
function verificarStatusTCP($ip, $porta = 80, $timeout = 1) {
    if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    
    try {
        $socket = @fsockopen($ip, $porta, $errno, $errstr, $timeout);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("Erro ao verificar IP $ip: " . $e->getMessage());
        return false;
    }
}

function verificarImpressora($ip, $timeout = 1) {
    return verificarStatusTCP($ip, 9100, $timeout);
}

/**
 * Sistema de monitoramento com cache
 */
function monitorarDispositivos($pdo) {
    $cache_file = __DIR__ . '/../cache/dispositivos.cache';
    $cache_time = 60; // 5 minutos de cache
    
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        return json_decode(file_get_contents($cache_file), true);
    }
    
    try {
        $stmt = $pdo->query("SELECT id, ip, tipo FROM dispositivos WHERE ativo = 1");
        $dispositivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT id, ip FROM impressoras WHERE ativo = 1");
        $impressoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $resultados = [
            'dispositivos' => ['total' => 0, 'ativos' => 0, 'detalhes' => []],
            'impressoras' => ['total' => 0, 'ativas' => 0, 'detalhes' => []],
            'atualizado' => date('H:i:s')
        ];
        
        foreach ($dispositivos as $dispositivo) {
            $status = verificarStatusTCP($dispositivo['ip']);
            $resultados['dispositivos']['total']++;
            if ($status) $resultados['dispositivos']['ativos']++;
            $resultados['dispositivos']['detalhes'][] = [
                'id' => $dispositivo['id'],
                'ip' => $dispositivo['ip'],
                'status' => $status ? 'Online' : 'Offline'
            ];
        }
        
        foreach ($impressoras as $impressora) {
            $status = verificarImpressora($impressora['ip']);
            $resultados['impressoras']['total']++;
            if ($status) $resultados['impressoras']['ativas']++;
            $resultados['impressoras']['detalhes'][] = [
                'id' => $impressora['id'],
                'ip' => $impressora['ip'],
                'status' => $status ? 'Online' : 'Offline'
            ];
        }
        
        file_put_contents($cache_file, json_encode($resultados));
        return $resultados;
    } catch (PDOException $e) {
        error_log("Erro ao monitorar dispositivos: " . $e->getMessage());
        return [
            'dispositivos' => ['total' => 0, 'ativos' => 0],
            'impressoras' => ['total' => 0, 'ativas' => 0],
            'atualizado' => 'Erro'
        ];
    }
}

/**
 * Funções para o dashboard
 */
function contarDispositivosRede($pdo) {
    try {
        $monitoramento = monitorarDispositivos($pdo);
        return [
            'total' => $monitoramento['dispositivos']['total'],
            'ativos' => $monitoramento['dispositivos']['ativos'],
            'atualizado' => $monitoramento['atualizado']
        ];
    } catch (Exception $e) {
        error_log("Erro em contarDispositivosRede: " . $e->getMessage());
        return ['total' => 0, 'ativos' => 0, 'atualizado' => 'Erro'];
    }
}

function contarImpressoras($pdo) {
    try {
        $monitoramento = monitorarDispositivos($pdo);
        return [
            'total' => $monitoramento['impressoras']['total'],
            'ativas' => $monitoramento['impressoras']['ativas'],
            'atualizado' => $monitoramento['atualizado']
        ];
    } catch (Exception $e) {
        error_log("Erro em contarImpressoras: " . $e->getMessage());
        return ['total' => 0, 'ativas' => 0, 'atualizado' => 'Erro'];
    }
}

function obterItensEstoque($pdo) {
    try {
        $stmt = $pdo->query("SELECT nome as item, local, quantidade FROM estoque ORDER BY quantidade ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao obter itens do estoque: " . $e->getMessage());
        return [];
    }
}

function contarItensEstoqueAtivos($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM estoque WHERE quantidade > 0");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erro ao contar itens ativos do estoque: " . $e->getMessage());
        return 0;
    }
}

function contarItensEstoqueCriticos($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM estoque WHERE quantidade < 3");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erro ao contar itens críticos do estoque: " . $e->getMessage());
        return 0;
    }
}

function obterLogsRecentes($pdo, $limite = 5) {
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