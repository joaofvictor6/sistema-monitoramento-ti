<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$response = [
    'dispositivos' => contarDispositivosAtivos($pdo),
    'impressoras' => contarImpressorasAtivas($pdo)
];

echo json_encode($response);
?>