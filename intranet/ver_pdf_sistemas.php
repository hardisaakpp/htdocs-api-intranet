<?php
// Detectar entorno: producción (Linux) o desarrollo (Windows)
$productionPath = '/storage/assets/doc/descarga/SISTEMAS';
$developmentPath = __DIR__ . '/../assets/doc/descarga/SISTEMAS';

// Usar ruta de producción si existe, sino usar ruta de desarrollo
if (file_exists($productionPath)) {
    $basePath = $productionPath;
} else {
    $basePath = $developmentPath;
}

$baseRealPath = realpath($basePath);

// Si la carpeta base no existe, mostrar error
if ($baseRealPath === false) {
    http_response_code(500);
    exit('Error: La carpeta base no existe. Ruta intentada: ' . htmlspecialchars($basePath));
}

$fileRel = isset($_GET['file']) ? $_GET['file'] : '';
$filePath = realpath($basePath . '/' . $fileRel);

if (!$filePath || strpos($filePath, $baseRealPath) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    exit('Archivo no encontrado o acceso denegado.');
}

// Determinar el tipo MIME según la extensión
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'mp3' => 'audio/mpeg',
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime',
    'mkv' => 'video/x-matroska',
    'webm' => 'video/webm',
];

$contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
