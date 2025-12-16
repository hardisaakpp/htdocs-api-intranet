<?php
/**
 * API Endpoint: Descargar documento
 * GET /api/intranet/documentos/descargar.php?id={id}
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('Método no permitido');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('ID de documento requerido');
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
        exit('Documento no encontrado');
    }
    
    $doc = $r[0];
    $nombreArchivo = $doc['doc_txt_nombre'] . $doc['tip_txt_extension'];
    $rutaRelativa = __DIR__ . '/../../../assets/doc/' . $doc['acc_txt_ruta'] . $doc_id . $doc['tip_txt_extension'];
    $rutaAbsoluta = realpath($rutaRelativa);
    
    if (!$rutaAbsoluta || !file_exists($rutaAbsoluta)) {
        http_response_code(404);
        exit('Archivo no encontrado');
    }
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($nombreArchivo) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($rutaAbsoluta));
    
    readfile($rutaAbsoluta);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Error al descargar documento');
}
?>


