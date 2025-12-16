<?php
/**
 * API Endpoint: Ver archivo
 * GET /api/intranet/archivos/ver.php?file={ruta_relativa}
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

if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ruta de archivo requerida']);
    exit;
}

// Detectar entorno: producción (Linux) o desarrollo (Windows)
$productionPath = '/storage/assets/doc/descarga/TH';
$developmentPath = __DIR__ . '/../../../assets/doc/descarga/TH';

if (file_exists($productionPath)) {
    $basePath = $productionPath;
} else {
    $basePath = $developmentPath;
}

$baseRealPath = realpath($basePath);

if ($baseRealPath === false) {
    http_response_code(500);
    echo json_encode(['error' => 'La carpeta base no existe']);
    exit;
}

$fileRel = $_GET['file'];
$filePath = realpath($basePath . '/' . $fileRel);

if (!$filePath || strpos($filePath, $baseRealPath) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Archivo no encontrado o acceso denegado']);
    exit;
}

// Construir URL para visualización (similar a ver_pdf_th.php)
$url = '/intranet/ver_pdf_th.php?file=' . urlencode($fileRel);

http_response_code(200);
echo json_encode([
    'url' => $url,
    'ruta_relativa' => $fileRel
], JSON_UNESCAPED_UNICODE);
?>


