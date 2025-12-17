<?php
/**
 * Procesador de operaciones CRUD para archivos de Talento Humano
 * Operaciones: subir, eliminar
 */

session_start();
require_once 'auth.php';

// Verificar autenticación - solo usuarios autenticados pueden modificar
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado. Debes iniciar sesión.']);
    exit;
}

// Detectar entorno
$productionPath = '/storage/assets/doc/descarga/TH';
$developmentPath = __DIR__ . '/../assets/doc/descarga/TH';

if (file_exists($productionPath)) {
    $basePath = $productionPath;
} else {
    $basePath = $developmentPath;
}

$baseRealPath = realpath($basePath);

if ($baseRealPath === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Carpeta base no existe']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Tipos de archivo permitidos
$allowedExtensions = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'zip', 'rar', 'txt',
    'mp3', 'wav', 'mp4', 'avi', 'mov', 'mkv', 'webm',
    'pptx', 'ppt'
];

// Tamaño máximo: 50MB
$maxFileSize = 50 * 1024 * 1024;

switch ($action) {
    case 'upload':
        handleUpload();
        break;
    case 'delete':
        handleDelete();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        exit;
}

function handleUpload() {
    global $basePath, $baseRealPath, $allowedExtensions, $maxFileSize;
    
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'Error al subir archivo';
        if (isset($_FILES['file']['error'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = 'El archivo excede el tamaño máximo permitido';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = 'El archivo se subió parcialmente';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = 'No se seleccionó ningún archivo';
                    break;
            }
        }
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }
    
    $file = $_FILES['file'];
    $targetPath = $_POST['path'] ?? '';
    $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === 'true';
    
    // Validar tamaño
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['success' => false, 'error' => 'Archivo demasiado grande. Máximo: 50MB']);
        exit;
    }
    
    // Validar extensión
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido. Extensiones permitidas: ' . implode(', ', $allowedExtensions)]);
        exit;
    }
    
    // Construir ruta completa
    $targetDir = $basePath . '/' . $targetPath;
    $targetDir = realpath($targetDir);
    
    // Si la ruta no existe, usar la base
    if ($targetDir === false) {
        $targetDir = $baseRealPath;
    }
    
    // Validar que la ruta esté dentro del directorio base
    if (strpos($targetDir, $baseRealPath) !== 0) {
        echo json_encode(['success' => false, 'error' => 'Ruta no válida']);
        exit;
    }
    
    // Verificar que sea un directorio
    if (!is_dir($targetDir)) {
        echo json_encode(['success' => false, 'error' => 'La ruta destino no es un directorio']);
        exit;
    }
    
    // Verificar permisos de escritura
    if (!is_writable($targetDir)) {
        echo json_encode(['success' => false, 'error' => 'Sin permisos de escritura en el directorio']);
        exit;
    }
    
    $targetFile = $targetDir . DIRECTORY_SEPARATOR . basename($file['name']);
    
    // Verificar si el archivo ya existe
    if (file_exists($targetFile) && !$overwrite) {
        echo json_encode(['success' => false, 'error' => 'El archivo ya existe', 'file_exists' => true]);
        exit;
    }
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Registrar en log (opcional)
        error_log("Archivo subido: " . $targetFile . " por usuario: " . ($_SESSION['usuario_nombre'] ?? 'desconocido'));
        
        echo json_encode([
            'success' => true,
            'message' => 'Archivo subido correctamente',
            'file' => basename($file['name']),
            'path' => $targetPath
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al mover el archivo al servidor']);
    }
}

function handleDelete() {
    global $basePath, $baseRealPath;
    
    $filePath = $_POST['file'] ?? $_GET['file'] ?? '';
    
    if (empty($filePath)) {
        echo json_encode(['success' => false, 'error' => 'Archivo no especificado']);
        exit;
    }
    
    // Normalizar la ruta: convertir backslashes a forward slashes y eliminar slashes duplicados
    $filePath = str_replace('\\', '/', $filePath);
    $filePath = trim($filePath, '/');
    
    // Construir ruta completa - normalizar también el basePath
    $normalizedBasePath = str_replace('\\', '/', $basePath);
    $fullPathToCheck = $normalizedBasePath . '/' . $filePath;
    
    // Intentar con realpath primero
    $fullPath = realpath($fullPathToCheck);
    
    // Si realpath falla, intentar construir manualmente
    if ($fullPath === false) {
        // Normalizar baseRealPath también
        $normalizedBaseRealPath = str_replace('\\', '/', $baseRealPath);
        $fullPath = $normalizedBaseRealPath . '/' . $filePath;
        
        // Convertir a la ruta real del sistema
        $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $fullPath);
        
        // Verificar que existe
        if (!file_exists($fullPath)) {
            // Intentar una vez más con realpath después de normalizar
            $fullPath = realpath($fullPath);
        }
    }
    
    // Validar seguridad - normalizar ambas rutas para comparar
    $normalizedFullPath = str_replace('\\', '/', $fullPath);
    $normalizedBaseRealPath = str_replace('\\', '/', $baseRealPath);
    
    if ($fullPath === false || strpos($normalizedFullPath, $normalizedBaseRealPath) !== 0) {
        echo json_encode([
            'success' => false, 
            'error' => 'Archivo no encontrado o acceso denegado'
        ]);
        exit;
    }
    
    // Verificar que existe
    if (!file_exists($fullPath)) {
        echo json_encode(['success' => false, 'error' => 'El archivo no existe']);
        exit;
    }
    
    // No permitir eliminar directorios (solo archivos)
    if (is_dir($fullPath)) {
        echo json_encode(['success' => false, 'error' => 'No se pueden eliminar carpetas desde aquí']);
        exit;
    }
    
    // Eliminar archivo
    if (unlink($fullPath)) {
        error_log("Archivo eliminado: " . $fullPath . " por usuario: " . ($_SESSION['usuario_nombre'] ?? 'desconocido'));
        
        echo json_encode([
            'success' => true,
            'message' => 'Archivo eliminado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el archivo. Verifica los permisos.']);
    }
}
?>

