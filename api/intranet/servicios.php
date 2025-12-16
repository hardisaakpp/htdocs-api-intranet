<?php
/**
 * API Endpoint: Servicios de Intranet
 * GET /api/intranet/servicios - Lista todos los servicios
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    include_once __DIR__ . '/../../cx/bd_scs.php';
    
    $q = "SELECT s.ser_txt_nombre, s.ser_txt_enlace 
          FROM plataforma_web.dbo.int_servicio AS s 
          WHERE s.ser_sts_estado = 1";
    
    $resultados = resp_simdim($q);
    
    // Convertir a formato JSON limpio
    $servicios = [];
    foreach ($resultados as $row) {
        $servicios[] = [
            'ser_txt_nombre' => $row['ser_txt_nombre'],
            'ser_txt_enlace' => $row['ser_txt_enlace']
        ];
    }
    
    http_response_code(200);
    echo json_encode($servicios, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener servicios',
        'message' => $e->getMessage()
    ]);
}
?>


