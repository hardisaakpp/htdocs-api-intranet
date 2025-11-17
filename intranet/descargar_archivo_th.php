<?php
// Detectar entorno: producción (Linux) o desarrollo (Windows)
$productionPath = '/storage/assets/doc/descarga/TH';
$developmentPath = __DIR__ . '/../assets/doc/descarga/TH';

// Usar ruta de producción si existe, sino usar ruta de desarrollo
if (file_exists($productionPath)) {
    $basePath = $productionPath;
} else {
    $basePath = $developmentPath;
}

$baseRealPath = realpath($basePath);

// Si la carpeta base no existe, mostrar error
if ($baseRealPath === false) {
    die("Error: La carpeta base no existe. Ruta intentada: " . htmlspecialchars($basePath));
}

$fileParam = isset($_GET['file']) ? $_GET['file'] : '';
$fullPath = realpath($basePath . '/' . $fileParam);

error_log("Parámetro recibido: " . $fileParam);
error_log("Ruta base: " . $basePath);
error_log("Ruta completa construida: " . $fullPath);

// Validar seguridad y existencia
if (!$fullPath || strpos($fullPath, $baseRealPath) !== 0 || !is_file($fullPath)) {
    die("Archivo no permitido o no encontrado");
}

// Descargar archivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fullPath));
readfile($fullPath);
exit;
