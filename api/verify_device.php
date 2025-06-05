<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

try {
    $db = (new Database())->connect();
    $deviceId = (int)$_GET['id'];
    
    // Obter IP do dispositivo
    $stmt = $db->prepare("SELECT ip FROM dispositivos WHERE id = ?");
    $stmt->execute([$deviceId]);
    $ip = $stmt->fetchColumn();
    
    if (!$ip) {
        echo json_encode(['success' => false, 'message' => 'Dispositivo não encontrado']);
        exit;
    }
    
    // Verificar status
    $status = verificarStatusTCP($ip) ? 'Online' : 'Offline';
    
    // Atualizar no banco de dados
    $stmt = $db->prepare("UPDATE dispositivos SET status = ?, ultima_verificacao = NOW() WHERE id = ?");
    $stmt->execute([$status, $deviceId]);
    
    echo json_encode(['success' => true, 'status' => $status]);
    
} catch (PDOException $e) {
    error_log("Erro ao verificar dispositivo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados']);
}
?>