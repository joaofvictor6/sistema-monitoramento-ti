<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(401);
    die(json_encode(['error' => 'Não autorizado']));
}

header('Content-Type: application/json');

try {
    $monitoramento = monitorarDispositivos($pdo);
    
    // Forçar nova verificação ignorando o cache
    if (isset($_GET['force']) && $_GET['force'] === 'true') {
        unlink(__DIR__ . '/../cache/dispositivos.cache');
        $monitoramento = monitorarDispositivos($pdo);
    }
    
    echo json_encode([
        'dispositivos' => [
            'total' => $monitoramento['dispositivos']['total'],
            'ativos' => $monitoramento['dispositivos']['ativos']
        ],
        'impressoras' => [
            'total' => $monitoramento['impressoras']['total'],
            'ativas' => $monitoramento['impressoras']['ativas']
        ],
        'atualizado' => $monitoramento['atualizado']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao verificar status']);
}
?>