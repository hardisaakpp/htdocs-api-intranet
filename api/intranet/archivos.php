<?php
/**
 * API Endpoint: Explorador de archivos
 * GET /api/intranet/archivos?path={path}&search={search}
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

// Detectar entorno: producción (Linux) o desarrollo (Windows)
$productionPath = '/storage/assets/doc/descarga/TH';
$developmentPath = __DIR__ . '/../../assets/doc/descarga/TH';

// Usar ruta de producción si existe, sino usar ruta de desarrollo
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

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$relPath = isset($_GET['path']) ? trim($_GET['path']) : '';
$currentPath = realpath($basePath . '/' . $relPath);

// Validación de seguridad
if ($currentPath === false || strpos($currentPath, $baseRealPath) !== 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no permitido']);
    exit;
}

// Función para buscar recursivamente
function buscarRecursivo($path, $searchTerm, $baseRealPath) {
    $resultados = [];
    $items = scandir($path);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $fullPath = realpath($path . DIRECTORY_SEPARATOR . $item);
        if ($fullPath === false) continue;
        
        $esDirectorio = is_dir($fullPath);
        $rutaRelativa = ltrim(str_replace($baseRealPath, '', $fullPath), '/');
        $extension = !$esDirectorio ? strtolower(pathinfo($item, PATHINFO_EXTENSION)) : '';
        
        if (stripos($item, $searchTerm) !== false) {
            $resultados[] = [
                'nombre' => $item,
                'es_directorio' => $esDirectorio,
                'ruta_relativa' => $rutaRelativa,
                'ruta_completa' => $fullPath,
                'extension' => $extension,
                'tamaño' => $esDirectorio ? 0 : filesize($fullPath),
                'fecha' => filemtime($fullPath)
            ];
        }
        
        if ($esDirectorio) {
            $subresultados = buscarRecursivo($fullPath, $searchTerm, $baseRealPath);
            $resultados = array_merge($resultados, $subresultados);
        }
    }
    
    return $resultados;
}

$items = [];

if ($search !== '') {
    $items = buscarRecursivo($baseRealPath, $search, $baseRealPath);
} else {
    foreach (scandir($currentPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $fullPath = realpath($currentPath . DIRECTORY_SEPARATOR . $item);
        if ($fullPath === false) continue;
        
        $esDirectorio = is_dir($fullPath);
        $extension = !$esDirectorio ? strtolower(pathinfo($item, PATHINFO_EXTENSION)) : '';
        
        $items[] = [
            'nombre' => $item,
            'es_directorio' => $esDirectorio,
            'ruta_relativa' => ltrim(str_replace($baseRealPath, '', $fullPath), '/'),
            'ruta_completa' => $fullPath,
            'extension' => $extension,
            'tamaño' => $esDirectorio ? 0 : filesize($fullPath),
            'fecha' => filemtime($fullPath)
        ];
    }
}

// Obtener nombre de carpeta actual si estamos dentro de una carpeta
$nombreCarpetaActual = '';
if (!empty($relPath) && $currentPath !== false) {
    $nombreCarpetaActual = basename($currentPath);
}

http_response_code(200);
echo json_encode([
    'items' => $items,
    'ruta_actual' => $relPath,
    'nombre_carpeta_actual' => $nombreCarpetaActual
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>


