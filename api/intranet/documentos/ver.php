<?php
/**
 * API Endpoint: Ver documento
 * GET /api/intranet/documentos/ver.php?id={id}
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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de documento requerido']);
    exit;
}

try {
    include_once __DIR__ . '/../../../cx/bd_scs.php';
    
    $doc_id = intval($_GET['id']);
    
    $q = "SELECT d.doc_txt_nombre, t.tip_txt_extension, a.acc_txt_ruta
          FROM plataforma_web.dbo.int_documento AS d
          INNER JOIN plataforma_web.dbo.int_doc_tipo AS t ON t.tip_id = d.tip_id
          INNER JOIN plataforma_web.dbo.int_doc_accion AS a ON a.acc_id = d.acc_id
          WHERE d.doc_id = $doc_id";
    
    $r = resp_simdim($q);
    
    if (!$r || count($r) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Documento no encontrado']);
        exit;
    }
    
    $doc = $r[0];
    // Construir URL similar a la del PHP original
    $rutaArchivo = $doc['acc_txt_ruta'] . $doc_id . $doc['tip_txt_extension'];
    $url = '/intranet/visualizador.php?MG$=ABC2880' . $rutaArchivo . '?809ID=465';
    
    http_response_code(200);
    echo json_encode([
        'url' => $url,
        'nombre' => $doc['doc_txt_nombre'] . $doc['tip_txt_extension']
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener documento',
        'message' => $e->getMessage()
    ]);
}
?>


