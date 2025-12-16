<?php
/**
 * API Endpoint: Documentos de Intranet
 * GET /api/intranet/documentos - Lista todos los documentos
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    include_once __DIR__ . '/../../cx/bd_scs.php';
    
    $q = "SELECT d.doc_id, d.doc_txt_nombre, e.dep_txt_nombre, c.cat_txt_nombre, 
                 t.tip_txt_nombre, t.tip_txt_extension, a.acc_id, a.acc_txt_ruta
          FROM plataforma_web.dbo.int_documento AS d
          INNER JOIN plataforma_web.dbo.int_loc_departamento AS e ON e.dep_id = d.dep_id
          INNER JOIN plataforma_web.dbo.int_doc_categoria AS c ON c.cat_id = d.cat_id
          INNER JOIN plataforma_web.dbo.int_doc_tipo AS t ON t.tip_id = d.tip_id
          INNER JOIN plataforma_web.dbo.int_doc_accion AS a ON a.acc_id = d.acc_id
          WHERE d.doc_sts_estado = 1 
            AND e.dep_sts_estado = 1 
            AND c.cat_sts_estado = 1 
            AND t.tip_sts_estado = 1 
            AND a.acc_sts_estado = 1 
            AND e.dep_id != 5
          ORDER BY a.acc_id";
    
    $resultados = resp_simdim($q);
    
    // Convertir a formato JSON limpio
    $documentos = [];
    foreach ($resultados as $row) {
        $documentos[] = [
            'doc_id' => (int)$row['doc_id'],
            'doc_txt_nombre' => $row['doc_txt_nombre'],
            'dep_txt_nombre' => $row['dep_txt_nombre'],
            'cat_txt_nombre' => $row['cat_txt_nombre'],
            'tip_txt_nombre' => $row['tip_txt_nombre'],
            'tip_txt_extension' => $row['tip_txt_extension'],
            'acc_id' => (int)$row['acc_id'],
            'acc_txt_ruta' => $row['acc_txt_ruta']
        ];
    }
    
    http_response_code(200);
    echo json_encode($documentos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener documentos',
        'message' => $e->getMessage()
    ]);
}
?>


