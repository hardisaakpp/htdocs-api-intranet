<?php
/**
 * API Endpoint: Imágenes del carrusel
 * GET /api/intranet/carrusel/imagenes - Lista todas las imágenes del carrusel
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
    $ruta = __DIR__ . '/../../../assets/img/intranet/carrusel/';
    
    if (!is_dir($ruta)) {
        http_response_code(404);
        echo json_encode(['error' => 'Carpeta de carrusel no existe']);
        exit;
    }
    
    $files = scandir($ruta);
    $imagenes = [];
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $ruta . $file;
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $imageExtensions)) {
            // Construir ruta relativa para la URL
            $rutaRelativa = '/assets/img/intranet/carrusel/' . $file;
            $imagenes[] = $rutaRelativa;
        }
    }
    
    // Ordenar alfabéticamente
    sort($imagenes);
    
    http_response_code(200);
    echo json_encode($imagenes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener imágenes',
        'message' => $e->getMessage()
    ]);
}
?>


