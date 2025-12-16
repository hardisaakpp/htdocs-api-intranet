<?php
/**
 * API Endpoint: Directorio de Intranet
 * GET /api/intranet/directorio - Lista todos los contactos
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
    
    $q = "SELECT d.dir_txt_area, d.dir_txt_colaborador, d.dir_val_extensión 
          FROM plataforma_web.dbo.int_directorio AS d 
          WHERE d.dir_sts_estado = 1";
    
    $resultados = resp_simdim($q);
    
    // Convertir a formato JSON limpio
    $contactos = [];
    foreach ($resultados as $row) {
        $contactos[] = [
            'dir_txt_area' => $row['dir_txt_area'],
            'dir_txt_colaborador' => $row['dir_txt_colaborador'],
            'dir_val_extensión' => $row['dir_val_extensión']
        ];
    }
    
    http_response_code(200);
    echo json_encode($contactos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener contactos',
        'message' => $e->getMessage()
    ]);
}
?>


