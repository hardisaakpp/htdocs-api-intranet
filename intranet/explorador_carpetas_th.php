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

// Si la carpeta base no existe, mostrar error descriptivo
if ($baseRealPath === false) {
    die("Error: La carpeta base no existe. Ruta intentada: " . htmlspecialchars($basePath));
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$relPath = isset($_GET['path']) ? trim($_GET['path']) : '';
$currentPath = realpath($basePath . '/' . $relPath);

// Validación de seguridad
if ($currentPath === false || strpos($currentPath, $baseRealPath) !== 0) {
    die("Acceso no permitido");
}

// Obtener el nombre de la carpeta actual si estamos dentro de una carpeta
$nombreCarpetaActual = '';
if (!empty($relPath) && $currentPath !== false) {
    // Obtener el nombre de la última carpeta del path
    $nombreCarpetaActual = basename($currentPath);
}

// Función para buscar recursivamente desde la raíz
function buscarRecursivo($path, $searchTerm, $baseRealPath) {
    $resultados = [];
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = realpath($path . DIRECTORY_SEPARATOR . $item);
        if ($fullPath === false) continue;

        $esDirectorio = is_dir($fullPath);
        // Normalizar la ruta relativa para usar siempre forward slashes
        $rutaRelativa = str_replace($baseRealPath, '', $fullPath);
        $rutaRelativa = str_replace('\\', '/', $rutaRelativa); // Normalizar a forward slashes
        $rutaRelativa = ltrim($rutaRelativa, '/\\'); // Eliminar slashes al inicio
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

// Función para obtener ícono según extensión
function obtenerIconoPorExtension($nombreArchivo) {
    $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    $iconos = [
        'pdf'  => 'https://cdn-icons-png.flaticon.com/512/337/337946.png',
        'doc'  => 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
        'docx' => 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
        'xls'  => 'https://cdn-icons-png.flaticon.com/512/732/732220.png',
        'xlsx' => 'https://cdn-icons-png.flaticon.com/512/732/732220.png',
        'jpg'  => 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
        'jpeg' => 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
        'png'  => 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
        'gif'  => 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
        'zip'  => 'https://cdn-icons-png.flaticon.com/512/888/888879.png',
        'rar'  => 'https://cdn-icons-png.flaticon.com/512/888/888879.png',
        'txt'  => 'https://cdn-icons-png.flaticon.com/512/3022/3022255.png',
        
        // Audio
        'mp3' => 'https://cdn-icons-png.flaticon.com/512/727/727245.png',
        'wav' => 'https://cdn-icons-png.flaticon.com/512/727/727245.png',
        'ogg' => 'https://cdn-icons-png.flaticon.com/512/727/727245.png',

        // Video
        'mp4' => 'https://cdn-icons-png.flaticon.com/512/727/727248.png',
        'avi' => 'https://cdn-icons-png.flaticon.com/512/727/727248.png',
        'mov' => 'https://cdn-icons-png.flaticon.com/512/727/727248.png',
        'mkv' => 'https://cdn-icons-png.flaticon.com/512/727/727248.png',
        'webm'=> 'https://cdn-icons-png.flaticon.com/512/727/727248.png',

        // Otros
        'default' => 'https://cdn-icons-png.flaticon.com/512/833/833524.png',
    ];

    return $iconos[$ext] ?? $iconos['default'];
}

$items = [];

if ($search !== '') {
    $items = buscarRecursivo($baseRealPath, $search, $baseRealPath);
} else {
    $items = [];
    foreach (scandir($currentPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = realpath($currentPath . DIRECTORY_SEPARATOR . $item);
        if ($fullPath === false) continue;
        $esDirectorio = is_dir($fullPath);
        $extension = !$esDirectorio ? strtolower(pathinfo($item, PATHINFO_EXTENSION)) : '';
        // Normalizar la ruta relativa para usar siempre forward slashes
        $rutaRelativa = str_replace($baseRealPath, '', $fullPath);
        $rutaRelativa = str_replace('\\', '/', $rutaRelativa); // Normalizar a forward slashes
        $rutaRelativa = ltrim($rutaRelativa, '/\\'); // Eliminar slashes al inicio
        $items[] = [
            'nombre' => $item,
            'es_directorio' => $esDirectorio,
            'ruta_relativa' => $rutaRelativa,
            'ruta_completa' => $fullPath,
            'extension' => $extension,
            'tamaño' => $esDirectorio ? 0 : filesize($fullPath),
            'fecha' => filemtime($fullPath)
        ];
    }
}
?>
<?php 
include_once 'header.php';
// Verificar si el usuario está autenticado para mostrar botones de edición
$usuarioAutenticado = isset($_SESSION['admin_autenticado']) && $_SESSION['admin_autenticado'] === true;
?>
<style>
    /* Estilos base para modo claro */
    .explorer-wrapper {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
        padding: 2rem;
        margin-top: 1rem;
    }
    .explorer-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #dee2e6;
    }
    .explorer-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .explorer-header h3 a {
        color: #212529;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .explorer-header h3 a:hover {
        color: #0d6efd;
        text-decoration: none;
    }
    
    /* Estilos para modo oscuro */
    [data-bs-theme="dark"] .explorer-wrapper {
        background: #161b22;
        box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        border: 1px solid #30363d;
    }
    [data-bs-theme="dark"] .explorer-header {
        border-bottom: 2px solid #30363d;
    }
    [data-bs-theme="dark"] .explorer-header h3 {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .explorer-header h3 a {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .explorer-header h3 a:hover {
        color: #58a6ff;
    }
    .folder-separator {
        color: #6c757d;
    }
    .folder-name {
        color: #0d6efd;
    }
    [data-bs-theme="dark"] .folder-separator {
        color: #8b949e;
    }
    [data-bs-theme="dark"] .folder-name {
        color: #58a6ff;
    }
    .folder-icon, .file-icon {
        width: 24px;
        height: 24px;
        margin-right: 10px;
        vertical-align: middle;
        object-fit: contain;
    }
    form.search-form {
        max-width: 500px;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.5rem;
        align-items: stretch;
    }
    form.search-form .form-control {
        background: #ffffff;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        color: #212529;
    }
    form.search-form .form-control::placeholder {
        color: #6c757d;
    }
    form.search-form .form-control:focus {
        background: #ffffff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        color: #212529;
    }
    form.search-form .btn {
        border-radius: 8px;
        padding: 0.6rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    /* Estilos para modo oscuro - formulario */
    [data-bs-theme="dark"] form.search-form .form-control {
        background: #0d1117;
        border: 2px solid #30363d;
        color: #e6edf3;
    }
    [data-bs-theme="dark"] form.search-form .form-control::placeholder {
        color: #6e7681;
    }
    [data-bs-theme="dark"] form.search-form .form-control:focus {
        background: #161b22;
        border-color: #2f81f7;
        box-shadow: 0 0 0 0.2rem rgba(47, 129, 247, 0.2);
        color: #e6edf3;
    }
    [data-bs-theme="dark"] form.search-form .btn-outline-secondary {
        background: #21262d;
        border-color: #30363d;
        color: #e6edf3;
    }
    [data-bs-theme="dark"] form.search-form .btn-outline-secondary:hover {
        background: #30363d;
        border-color: #484f58;
        color: #ffffff;
    }
    [data-bs-theme="dark"] form.search-form .btn-outline-danger {
        background: #21262d;
        border-color: #da3633;
        color: #f85149;
    }
    [data-bs-theme="dark"] form.search-form .btn-outline-danger:hover {
        background: #da3633;
        border-color: #f85149;
        color: #ffffff;
    }
    form.search-form .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .table {
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 0;
        border: 1px solid #dee2e6;
        color: #212529;
    }
    .table thead th {
        background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }
    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: #212529;
    }
    
    /* Estilos para modo oscuro - tabla */
    [data-bs-theme="dark"] .table {
        background: #161b22;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        border: 1px solid #30363d;
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .table thead th {
        background: linear-gradient(to bottom, #21262d, #161b22);
        border-bottom: 2px solid #30363d;
        color: #8b949e;
    }
    [data-bs-theme="dark"] .table tbody tr {
        border-bottom: 1px solid #21262d;
    }
    [data-bs-theme="dark"] .table tbody tr:hover {
        background-color: #1c2128;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    [data-bs-theme="dark"] .table tbody td {
        color: #e6edf3;
    }
    .folder-link {
        cursor: pointer;
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .folder-link:hover {
        text-decoration: none;
        color: #0a58ca;
    }
    .folder-row {
        transition: all 0.2s ease;
    }
    .folder-row:hover {
        background-color: #f8f9fa !important;
        cursor: pointer;
    }
    
    /* Estilos para modo oscuro - enlaces y filas */
    [data-bs-theme="dark"] .folder-link {
        color: #58a6ff;
    }
    [data-bs-theme="dark"] .folder-link:hover {
        color: #79c0ff;
        text-shadow: 0 0 8px rgba(88, 166, 255, 0.4);
    }
    [data-bs-theme="dark"] .folder-row:hover {
        background-color: #1c2128 !important;
    }
    .file-preview-trigger {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .text-muted {
        color: #6c757d !important;
        font-size: 0.9rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }
    
    /* Estilos para modo oscuro - texto y estados vacíos */
    [data-bs-theme="dark"] .text-muted {
        color: #8b949e !important;
    }
    [data-bs-theme="dark"] .empty-state {
        color: #8b949e;
    }
    .empty-state::before {
        content: "📂";
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .file-preview {
        position: fixed;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 10px;
        z-index: 10000;
        width: 320px;
        height: 310px;
        opacity: 0;
        transform: scale(0.9) translateY(-10px);
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        pointer-events: none;
        visibility: hidden;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }
    .file-preview.active {
        opacity: 1;
        transform: scale(1) translateY(0);
        visibility: visible;
    }
    .file-preview .preview-title {
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 8px;
        color: #212529;
        word-break: break-word;
        flex-shrink: 0;
        max-height: 36px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .file-preview img.preview-image {
        width: 100%;
        height: 260px;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        border-radius: 4px;
        background: #f8f9fa;
        opacity: 0;
        animation: fadeInImage 0.4s ease-out 0.1s forwards;
        flex-shrink: 0;
    }
    .file-preview iframe.preview-iframe {
        width: 100%;
        height: 260px;
        border: none;
        border-radius: 4px;
        margin: 0;
        opacity: 0;
        animation: fadeInImage 0.4s ease-out 0.1s forwards;
        flex-shrink: 0;
        background: #f8f9fa;
    }
    
    /* Estilos para modo oscuro - preview */
    [data-bs-theme="dark"] .file-preview {
        background: #161b22;
        border: 2px solid #2f81f7;
        box-shadow: 0 4px 12px rgba(0,0,0,0.6);
    }
    [data-bs-theme="dark"] .file-preview .preview-title {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .file-preview img.preview-image {
        background: #0d1117;
    }
    [data-bs-theme="dark"] .file-preview iframe.preview-iframe {
        background: #0d1117;
    }
    
    /* Estilos para nombres de archivos */
    .file-name {
        color: #212529;
    }
    .file-name-pdf, .file-name-video {
        cursor: pointer;
        color: #0d6efd;
        text-decoration: underline;
    }
    .file-name-pdf:hover, .file-name-video:hover {
        color: #0a58ca;
    }
    [data-bs-theme="dark"] .file-name {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .file-name-pdf, [data-bs-theme="dark"] .file-name-video {
        color: #58a6ff;
    }
    [data-bs-theme="dark"] .file-name-pdf:hover, [data-bs-theme="dark"] .file-name-video:hover {
        color: #79c0ff;
    }
    
    /* Estilos para modal PDF */
    .pdf-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.85);
        z-index: 1000;
    }
    .pdf-modal-content {
        position: relative;
        width: 90%;
        height: 90%;
        margin: 5% auto;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    .pdf-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        color: #212529;
        padding: 12px 16px;
    }
    .pdf-modal-filename {
        font-weight: 600;
        color: #212529;
    }
    .pdf-modal-btn {
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 8px;
        transition: all 0.2s;
        border: 1px solid #dee2e6;
        background: #ffffff;
        color: #212529;
    }
    .pdf-modal-btn:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    .pdf-modal-btn-close {
        background: #dc3545;
        border: 1px solid #dc3545;
        color: #fff;
    }
    .pdf-modal-btn-close:hover {
        background: #c82333;
        border-color: #bd2130;
    }
    .pdf-viewer {
        width: 100%;
        height: calc(100% - 60px);
        border: none;
        background: #f8f9fa;
    }
    
    /* Estilos para modo oscuro - modal PDF */
    [data-bs-theme="dark"] .pdf-modal-content {
        background: #161b22;
        border: 1px solid #30363d;
        box-shadow: 0 8px 32px rgba(0,0,0,0.6);
    }
    [data-bs-theme="dark"] .pdf-modal-header {
        background: #21262d;
        border-bottom: 1px solid #30363d;
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .pdf-modal-filename {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .pdf-modal-btn {
        background: #21262d;
        border: 1px solid #30363d;
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .pdf-modal-btn:hover {
        background: #30363d;
        border-color: #484f58;
    }
    [data-bs-theme="dark"] .pdf-viewer {
        background: #0d1117;
    }
    .video-viewer {
        width: 100%;
        height: calc(100% - 60px);
        border: none;
        background: #000000;
        outline: none;
    }
    [data-bs-theme="dark"] .video-viewer {
        background: #000000;
    }
    @keyframes fadeInImage {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .file-preview .preview-info {
        font-size: 12px;
        color: #666;
        border-top: 1px solid #eee;
        padding-top: 8px;
        margin-top: 8px;
    }
    .file-preview .preview-detail {
        margin: 4px 0;
    }
    
    /* Estilos para botones de acción */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    [data-bs-theme="dark"] .btn-danger {
        background-color: #da3633;
        border-color: #da3633;
    }
    [data-bs-theme="dark"] .btn-danger:hover {
        background-color: #f85149;
        border-color: #f85149;
    }
    
    /* Botón flotante */
    .floating-upload-btn {
        position: fixed;
        bottom: 100px;
        right: 30px;
        z-index: 1000;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .floating-upload-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0,0,0,0.4);
    }
    .floating-upload-btn:active {
        transform: scale(0.95);
    }
    
    /* Estilos mejorados para modal de subida */
    #uploadModal .modal-dialog {
        max-width: 500px;
    }
    
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    /* Área de arrastrar y soltar mejorada */
    .file-upload-area {
        border: 3px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem 1.5rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .file-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
        transform: translateY(-2px);
    }
    
    .file-upload-area.dragover {
        border-color: #667eea;
        background: #e8edff;
        transform: scale(1.02);
    }
    
    .file-upload-area .upload-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
        display: block;
        opacity: 0.7;
    }
    
    .file-upload-area .upload-text {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.4rem;
    }
    
    .file-upload-area .upload-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }
    
    .file-upload-area .upload-button {
        display: inline-block;
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .file-upload-area .upload-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .file-input-hidden {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        overflow: hidden;
    }
    
    .file-selected-info {
        display: none;
        margin-top: 1.5rem;
        padding: 1rem;
        background: #e7f3ff;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
    
    .file-selected-info.show {
        display: block;
    }
    
    .file-selected-info .file-name {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .file-selected-info .file-size {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Checkbox mejorado */
    .form-check {
        padding: 1rem 1rem 1rem 2.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }
    
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin: 0;
        flex-shrink: 0;
        cursor: pointer;
        position: relative;
        left: 0;
    }
    
    .form-check-label {
        margin: 0;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        flex: 1;
    }
    
    /* Barra de progreso mejorada */
    #uploadProgress {
        margin-top: 1.5rem;
        height: 8px;
        border-radius: 10px;
        overflow: hidden;
        background: #e9ecef;
    }
    
    #uploadProgress .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    
    /* Mensajes mejorados */
    #uploadMessage {
        margin-top: 1rem;
        border-radius: 8px;
        padding: 1rem;
        font-weight: 500;
    }
    
    #uploadMessage.alert-success {
        background: #d1e7dd;
        border: 1px solid #badbcc;
        color: #0f5132;
    }
    
    #uploadMessage.alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c2c7;
        color: #842029;
    }
    
    #uploadMessage.alert-warning {
        background: #fff3cd;
        border: 1px solid #ffecb5;
        color: #664d03;
    }
    
    /* Footer del modal mejorado */
    .modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    
    .modal-footer .btn {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .modal-footer .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    /* Modo oscuro */
    [data-bs-theme="dark"] .modal-content {
        background-color: #161b22;
        border-color: #30363d;
    }
    
    [data-bs-theme="dark"] .modal-header {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }
    
    [data-bs-theme="dark"] .file-upload-area {
        background: #0d1117;
        border-color: #30363d;
    }
    
    [data-bs-theme="dark"] .file-upload-area:hover {
        background: #161b22;
        border-color: #58a6ff;
    }
    
    [data-bs-theme="dark"] .file-upload-area.dragover {
        background: #1c2128;
        border-color: #58a6ff;
    }
    
    [data-bs-theme="dark"] .file-upload-area .upload-text {
        color: #e6edf3;
    }
    
    [data-bs-theme="dark"] .file-upload-area .upload-hint {
        color: #8b949e;
    }
    
    [data-bs-theme="dark"] .file-selected-info {
        background: #1c2128;
        border-left-color: #58a6ff;
    }
    
    [data-bs-theme="dark"] .file-selected-info .file-name {
        color: #e6edf3;
    }
    
    [data-bs-theme="dark"] .file-selected-info .file-size {
        color: #8b949e;
    }
    
    [data-bs-theme="dark"] .form-check {
        background: #0d1117;
    }
    
    [data-bs-theme="dark"] .form-check-label {
        color: #e6edf3;
    }
    
    [data-bs-theme="dark"] .modal-footer {
        background: #0d1117;
        border-top-color: #30363d;
    }
    
    [data-bs-theme="dark"] #uploadProgress {
        background: #21262d;
    }
    
    [data-bs-theme="dark"] #uploadMessage.alert-success {
        background: #1a472a;
        border-color: #2ea043;
        color: #3fb950;
    }
    
    [data-bs-theme="dark"] #uploadMessage.alert-danger {
        background: #3d1f1f;
        border-color: #da3633;
        color: #f85149;
    }
    
    [data-bs-theme="dark"] #uploadMessage.alert-warning {
        background: #3d2e1f;
        border-color: #d29922;
        color: #d29922;
    }
</style>
<body class="p-4">
<div class="container">
    <div class="explorer-wrapper">
        <div class="explorer-header">
            <h3>
                <a href="explorador_carpetas_th.php">📁 Explorador de Carpetas - TH</a>
                <?php if (!empty($nombreCarpetaActual)): ?>
                    <span class="folder-separator" style="margin: 0 0.5rem;">/</span>
                    <span class="folder-name"><?php echo htmlspecialchars($nombreCarpetaActual); ?></span>
                <?php endif; ?>
            </h3>
        </div>

        <!-- Formulario de búsqueda -->
        <form class="search-form" method="get">
            <input type="text" name="search" class="form-control" placeholder="🔍 Buscar archivo o carpeta..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            <?php if ($search): ?>
                <a href="explorador_carpetas_th.php" class="btn btn-outline-danger">Limpiar</a>
            <?php endif; ?>
        </form>

    <!-- Tabla de resultados -->
    <div style="overflow-x: auto;">
    <table class="table table-hover table-sm">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Ruta</th>
            <th>Tipo</th>
            <?php if ($usuarioAutenticado): ?>
            <th>Acciones</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="<?php echo $usuarioAutenticado ? '4' : '3'; ?>" class="empty-state">No se encontraron resultados.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <?php if ($item['es_directorio']): ?>
                    <tr class="folder-row" onclick="window.location.href='?path=<?php echo urlencode($item['ruta_relativa']); ?>'" style="cursor: pointer;">
                        <td>
                            <img src="https://cdn-icons-png.flaticon.com/512/715/715676.png" class="folder-icon">
                            <a href="?path=<?php echo urlencode($item['ruta_relativa']); ?>" class="folder-link">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </a>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($item['ruta_relativa']); ?></td>
                        <td>Carpeta</td>
                        <?php if ($usuarioAutenticado): ?>
                        <td></td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php 
                    $isPdf = $item['extension'] === 'pdf';
                    $isImagen = in_array($item['extension'], ['jpg', 'jpeg', 'png', 'gif']);
                    $isVideo = in_array($item['extension'], ['mp4', 'avi', 'mov', 'mkv', 'webm']);
                    $tamañoFormateado = $item['tamaño'] > 0 ? number_format($item['tamaño'] / 1024, 2) . ' KB' : '0 KB';
                    if ($item['tamaño'] > 1024 * 1024) {
                        $tamañoFormateado = number_format($item['tamaño'] / (1024 * 1024), 2) . ' MB';
                    }
                    $fechaFormateada = date('d/m/Y H:i', $item['fecha']);
                    $previewUrl = '';
                    $previewType = 'info';
                    if ($isImagen) {
                        $previewUrl = 'ver_pdf_th.php?file=' . urlencode($item['ruta_relativa']);
                        $previewType = 'image';
                    } elseif ($isPdf) {
                        $previewUrl = 'ver_pdf_th.php?file=' . urlencode($item['ruta_relativa']);
                        $previewType = 'pdf';
                    } elseif ($isVideo) {
                        $previewUrl = 'ver_pdf_th.php?file=' . urlencode($item['ruta_relativa']);
                        $previewType = 'video';
                    }
                    ?>
                    <tr>
                        <td>
                            <div class="file-preview-trigger" 
                                 data-preview-type="<?php echo $previewType; ?>"
                                 data-preview-url="<?php echo htmlspecialchars($previewUrl); ?>"
                                 data-file-name="<?php echo htmlspecialchars($item['nombre']); ?>"
                                 data-file-size="<?php echo htmlspecialchars($tamañoFormateado); ?>"
                                 data-file-date="<?php echo htmlspecialchars($fechaFormateada); ?>"
                                 data-file-type="<?php echo htmlspecialchars(strtoupper($item['extension'] ?: 'Archivo')); ?>">
                                <img src="<?php echo obtenerIconoPorExtension($item['nombre']); ?>"
                                    class="file-icon"
                                    style="cursor: <?php echo ($isPdf || $isVideo) ? 'pointer' : 'default'; ?>"
                                    onclick="<?php 
                                        if ($isPdf) {
                                            echo "openPDFModal('ver_pdf_th.php?file=" . urlencode($item['ruta_relativa']) . "', '" . htmlspecialchars($item['nombre']) . "')";
                                        } elseif ($isVideo) {
                                            echo "openVideoModal('ver_pdf_th.php?file=" . urlencode($item['ruta_relativa']) . "', '" . htmlspecialchars($item['nombre']) . "')";
                                        }
                                    ?>">
                                <?php if ($isPdf): ?>
                                    <span class="file-name-pdf"
                                        onclick="event.stopPropagation(); openPDFModal('ver_pdf_th.php?file=<?php echo urlencode($item['ruta_relativa']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>')">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </span>
                                <?php elseif ($isVideo): ?>
                                    <span class="file-name-video"
                                        onclick="event.stopPropagation(); openVideoModal('ver_pdf_th.php?file=<?php echo urlencode($item['ruta_relativa']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>')">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="file-name"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($item['ruta_relativa']); ?></td>
                        <td>Archivo</td>
                        <?php if ($usuarioAutenticado): ?>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="event.stopPropagation(); confirmDelete('<?php echo htmlspecialchars($item['ruta_relativa']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>')"
                                    title="Eliminar archivo">
                                🗑️
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>
</div>

<!-- Modal Visor PDF -->
<!-- Preview Tooltip -->
<div id="filePreview" class="file-preview"></div>

<div id="pdfModal" class="pdf-modal">
  <div class="pdf-modal-content">
    <div class="pdf-modal-header">
      <span id="modalFilename" class="pdf-modal-filename"></span>
      <div>
        <button onclick="printPDF()" class="pdf-modal-btn">🖨 Imprimir</button>
        <a id="downloadLink" class="pdf-modal-btn" download target="_blank" style="text-decoration:none; display:inline-block;">⬇ Descargar</a>
        <button onclick="closeModal()" class="pdf-modal-btn pdf-modal-btn-close">✖ Cerrar</button>
      </div>
    </div>
    <iframe id="pdfViewer" class="pdf-viewer"></iframe>
  </div>
</div>

<!-- Modal Visor Video -->
<div id="videoModal" class="pdf-modal">
  <div class="pdf-modal-content">
    <div class="pdf-modal-header">
      <span id="videoModalFilename" class="pdf-modal-filename"></span>
      <div>
        <a id="videoDownloadLink" class="pdf-modal-btn" download target="_blank" style="text-decoration:none; display:inline-block;">⬇ Descargar</a>
        <button onclick="closeVideoModal()" class="pdf-modal-btn pdf-modal-btn-close">✖ Cerrar</button>
      </div>
    </div>
    <video id="videoViewer" class="video-viewer" controls autoplay>
      Tu navegador no soporta la reproducción de video.
    </video>
  </div>
</div>

<script>
    function openPDFModal(pdfUrl, filename) {
        document.getElementById('pdfViewer').src = pdfUrl;
        document.getElementById('downloadLink').href = pdfUrl;
        document.getElementById('modalFilename').innerText = filename;
        document.getElementById('pdfModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfViewer').src = '';
    }

    function printPDF() {
        const iframe = document.getElementById('pdfViewer');
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }

    function openVideoModal(videoUrl, filename) {
        const videoElement = document.getElementById('videoViewer');
        const downloadLink = document.getElementById('videoDownloadLink');
        const modalFilename = document.getElementById('videoModalFilename');
        const modal = document.getElementById('videoModal');
        
        videoElement.src = videoUrl;
        downloadLink.href = videoUrl;
        modalFilename.innerText = filename;
        modal.style.display = 'block';
        
        // Cargar el video
        videoElement.load();
        
        // Intentar reproducir automáticamente
        // Nota: Algunos navegadores pueden bloquear el autoplay con sonido
        videoElement.play().catch(function(error) {
            // Si el autoplay falla (por políticas del navegador), el usuario puede hacer clic en play manualmente
            console.log('Autoplay bloqueado por el navegador:', error);
        });
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const videoElement = document.getElementById('videoViewer');
        
        modal.style.display = 'none';
        // Pausar y limpiar el video
        videoElement.pause();
        videoElement.src = '';
    }
    
    // Cerrar modal de video con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const videoModal = document.getElementById('videoModal');
            if (videoModal && videoModal.style.display === 'block') {
                closeVideoModal();
            }
            const pdfModal = document.getElementById('pdfModal');
            if (pdfModal && pdfModal.style.display === 'block') {
                closeModal();
            }
        }
    });
    
    // Cerrar modal de video al hacer click fuera del contenido
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });

    // Sistema de preview al hacer hover
    let previewTimeout;
    const previewElement = document.getElementById('filePreview');
    let currentPreviewElement = null;

    document.addEventListener('DOMContentLoaded', function() {
        const triggers = document.querySelectorAll('.file-preview-trigger');
        
        triggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function(e) {
                const element = this;
                clearTimeout(previewTimeout);
                
                previewTimeout = setTimeout(() => {
                    showPreview(element, e);
                }, 300); // Delay de 300ms antes de mostrar
            });
            
            trigger.addEventListener('mouseleave', function() {
                clearTimeout(previewTimeout);
                hidePreview();
            });
            
            trigger.addEventListener('mousemove', function(e) {
                if (previewElement.classList.contains('active')) {
                    updatePreviewPosition(e);
                }
            });
        });
    });

    function showPreview(element, event) {
        const previewType = element.getAttribute('data-preview-type');
        const previewUrl = element.getAttribute('data-preview-url');
        const fileName = element.getAttribute('data-file-name');
        const fileSize = element.getAttribute('data-file-size');
        const fileType = element.getAttribute('data-file-type');
        
        let previewContent = '<div class="preview-title">' + escapeHtml(fileName) + '</div>';
        
        if (previewType === 'image' && previewUrl) {
            previewContent += '<img src="' + escapeHtml(previewUrl) + '" class="preview-image" alt="Preview">';
        } else if (previewType === 'pdf' && previewUrl) {
            previewContent += '<iframe src="' + escapeHtml(previewUrl) + '" class="preview-iframe"></iframe>';
        } else if (previewType === 'video' && previewUrl) {
            previewContent += '<div style="padding: 2rem; text-align: center; color: #6c757d;">';
            previewContent += '<div style="font-size: 3rem; margin-bottom: 1rem;">🎬</div>';
            previewContent += '<div style="font-weight: 600; margin-bottom: 0.5rem;">Video ' + escapeHtml(fileType) + '</div>';
            previewContent += '<div style="font-size: 0.85rem;">Tamaño: ' + escapeHtml(fileSize) + '</div>';
            previewContent += '<div style="font-size: 0.75rem; margin-top: 0.5rem; color: #0d6efd;">Haz clic para reproducir</div>';
            previewContent += '</div>';
        }
        
        previewElement.innerHTML = previewContent;
        previewElement.classList.add('active');
        currentPreviewElement = element;
        updatePreviewPosition(event);
    }

    function hidePreview() {
        previewElement.classList.remove('active');
        previewElement.innerHTML = '';
        currentPreviewElement = null;
    }

    function updatePreviewPosition(event) {
        const preview = previewElement;
        const margin = 10;
        let left = event.pageX + margin;
        let top = event.pageY + margin;
        
        // Ajustar si se sale por la derecha
        if (left + preview.offsetWidth > window.innerWidth) {
            left = event.pageX - preview.offsetWidth - margin;
        }
        
        // Ajustar si se sale por abajo
        if (top + preview.offsetHeight > window.innerHeight) {
            top = event.pageY - preview.offsetHeight - margin;
        }
        
        preview.style.left = left + 'px';
        preview.style.top = top + 'px';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    // Funciones CRUD
    function showUploadModal() {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        document.getElementById('uploadPath').value = '<?php echo htmlspecialchars($relPath); ?>';
        document.getElementById('uploadForm').reset();
        document.getElementById('uploadMessage').style.display = 'none';
        document.getElementById('uploadProgress').style.display = 'none';
        document.getElementById('fileSelectedInfo').classList.remove('show');
        document.getElementById('uploadButton').disabled = true;
        modal.show();
        
        // Configurar eventos de arrastrar y soltar
        setupDragAndDrop();
    }
    
    function setupDragAndDrop() {
        const uploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');
        
        // Prevenir comportamiento por defecto del navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Efectos visuales al arrastrar
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });
        
        // Manejar archivo soltado
        uploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        }, false);
        
        // Manejar selección de archivo
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });
    }
    
    function handleFileSelect(file) {
        const fileInfo = document.getElementById('fileSelectedInfo');
        const fileName = document.getElementById('selectedFileName');
        const fileSize = document.getElementById('selectedFileSize');
        const uploadButton = document.getElementById('uploadButton');
        
        // Mostrar información del archivo
        fileName.textContent = '📄 ' + file.name;
        
        // Formatear tamaño
        let sizeText = '';
        if (file.size < 1024) {
            sizeText = file.size + ' bytes';
        } else if (file.size < 1024 * 1024) {
            sizeText = (file.size / 1024).toFixed(2) + ' KB';
        } else {
            sizeText = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        fileSize.textContent = 'Tamaño: ' + sizeText;
        
        // Validar tamaño máximo (50MB)
        const maxSize = 50 * 1024 * 1024;
        if (file.size > maxSize) {
            fileSize.textContent = '❌ Tamaño excede el límite de 50MB';
            fileSize.style.color = '#dc3545';
            uploadButton.disabled = true;
            fileInfo.classList.add('show');
            return;
        }
        
        fileSize.style.color = '';
        fileInfo.classList.add('show');
        uploadButton.disabled = false;
    }
    
    function uploadFile() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);
        const progressBar = document.getElementById('uploadProgress');
        const progressBarInner = progressBar.querySelector('.progress-bar');
        const messageDiv = document.getElementById('uploadMessage');
        const uploadButton = document.getElementById('uploadButton');
        
        // Validar que se haya seleccionado un archivo
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            messageDiv.style.display = 'block';
            messageDiv.className = 'alert alert-warning';
            messageDiv.textContent = '⚠️ Por favor selecciona un archivo';
            return;
        }
        
        // Deshabilitar botón durante la subida
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';
        
        progressBar.style.display = 'block';
        progressBarInner.style.width = '0%';
        messageDiv.style.display = 'none';
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBarInner.style.width = percentComplete + '%';
                progressBarInner.textContent = Math.round(percentComplete) + '%';
            }
        });
        
        xhr.addEventListener('load', function() {
            progressBar.style.display = 'none';
            messageDiv.style.display = 'block';
            uploadButton.disabled = false;
            uploadButton.innerHTML = '<span style="margin-right: 0.5rem;">📤</span>Subir Archivo';
            
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        messageDiv.className = 'alert alert-success';
                        messageDiv.innerHTML = '✅ <strong>' + response.message + '</strong><br>La página se recargará en breve...';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        messageDiv.className = 'alert alert-danger';
                        messageDiv.innerHTML = '❌ <strong>Error:</strong> ' + response.error;
                        if (response.file_exists) {
                            if (confirm('El archivo ya existe. ¿Deseas sobrescribirlo?')) {
                                document.getElementById('overwriteCheck').checked = true;
                                uploadFile();
                            }
                        }
                    }
                } catch (e) {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.innerHTML = '❌ <strong>Error:</strong> No se pudo procesar la respuesta del servidor';
                }
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '❌ <strong>Error:</strong> No se pudo subir el archivo (Código: ' + xhr.status + ')';
            }
        });
        
        xhr.addEventListener('error', function() {
            progressBar.style.display = 'none';
            messageDiv.style.display = 'block';
            messageDiv.className = 'alert alert-danger';
            messageDiv.innerHTML = '❌ <strong>Error de conexión:</strong> Verifica tu conexión a internet.';
            uploadButton.disabled = false;
            uploadButton.innerHTML = '<span style="margin-right: 0.5rem;">📤</span>Subir Archivo';
        });
        
        xhr.open('POST', 'procesar_archivos_th.php');
        xhr.send(formData);
    }
    
    function confirmDelete(filePath, fileName) {
        if (confirm('¿Estás seguro de eliminar el archivo "' + fileName + '"?\n\nEsta acción no se puede deshacer.')) {
            deleteFile(filePath);
        }
    }
    
    function deleteFile(filePath) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('file', filePath);
        
        const xhr = new XMLHttpRequest();
        
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.error);
                    }
                } catch (e) {
                    alert('Error al procesar la respuesta del servidor');
                }
            } else {
                alert('Error al eliminar el archivo (Código: ' + xhr.status + ')');
            }
        });
        
        xhr.addEventListener('error', function() {
            alert('Error de conexión. Verifica tu conexión a internet.');
        });
        
        xhr.open('POST', 'procesar_archivos_th.php');
        xhr.send(formData);
    }
</script>

<!-- Modal para subir archivo -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <span style="font-size: 1.5rem;">📤</span> Subir Archivo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="path" id="uploadPath" value="<?php echo htmlspecialchars($relPath); ?>">
                    
                    <!-- Área de arrastrar y soltar -->
                    <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('fileInput').click()">
                        <span class="upload-icon">📁</span>
                        <div class="upload-text">Arrastra y suelta tu archivo aquí</div>
                        <div class="upload-hint">o haz clic para seleccionar</div>
                        <button type="button" class="upload-button">Seleccionar Archivo</button>
                        <input type="file" class="file-input-hidden" id="fileInput" name="file" required>
                    </div>
                    
                    <!-- Información del archivo seleccionado -->
                    <div class="file-selected-info" id="fileSelectedInfo">
                        <div class="file-name" id="selectedFileName"></div>
                        <div class="file-size" id="selectedFileSize"></div>
                    </div>
                    
                    <!-- Opciones adicionales -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="overwriteCheck" name="overwrite" value="true">
                        <label class="form-check-label" for="overwriteCheck">
                            <strong>Sobrescribir</strong> si el archivo ya existe
                        </label>
                    </div>
                    
                    <!-- Información de límites -->
                    <div style="margin-top: 1rem; padding: 0.75rem; background: #f8f9fa; border-radius: 8px; font-size: 0.85rem; color: #6c757d;">
                        <strong>ℹ️ Información:</strong><br>
                        • Tamaño máximo: <strong>50MB</strong><br>
                        • Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, imágenes, videos, etc.
                    </div>
                    
                    <!-- Barra de progreso -->
                    <div id="uploadProgress" class="progress" style="display: none; margin-top: 1.5rem;">
                        <div class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    
                    <!-- Mensajes -->
                    <div id="uploadMessage" class="alert" style="display: none; margin-top: 1rem;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="uploadButton" onclick="uploadFile()" disabled>
                    <span style="margin-right: 0.5rem;">📤</span>Subir Archivo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante para subir archivo (solo si está autenticado) -->
<?php if ($usuarioAutenticado): ?>
<button type="button" class="btn btn-primary floating-upload-btn" 
        onclick="showUploadModal()" 
        title="Subir archivo a esta carpeta">
    ➕
</button>
<?php endif; ?>

</body>
<?php include_once 'footer.php'; ?>
