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
?>
<?php include_once 'header.php'; ?>
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
    .file-name-pdf {
        cursor: pointer;
        color: #0d6efd;
        text-decoration: underline;
    }
    .file-name-pdf:hover {
        color: #0a58ca;
    }
    [data-bs-theme="dark"] .file-name {
        color: #e6edf3;
    }
    [data-bs-theme="dark"] .file-name-pdf {
        color: #58a6ff;
    }
    [data-bs-theme="dark"] .file-name-pdf:hover {
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
        </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="3" class="empty-state">No se encontraron resultados.</td></tr>
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
                    </tr>
                <?php else: ?>
                    <?php 
                    $isPdf = $item['extension'] === 'pdf';
                    $isImagen = in_array($item['extension'], ['jpg', 'jpeg', 'png', 'gif']);
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
                                    style="cursor: <?php echo $isPdf ? 'pointer' : 'default'; ?>"
                                    onclick="<?php if ($isPdf) echo "openPDFModal('ver_pdf_th.php?file=" . urlencode($item['ruta_relativa']) . "', '" . htmlspecialchars($item['nombre']) . "')"; ?>">
                                <?php if ($isPdf): ?>
                                    <span class="file-name-pdf"
                                        onclick="event.stopPropagation(); openPDFModal('ver_pdf_th.php?file=<?php echo urlencode($item['ruta_relativa']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>')">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="file-name"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($item['ruta_relativa']); ?></td>
                        <td>Archivo</td>
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
        
        let previewContent = '<div class="preview-title">' + escapeHtml(fileName) + '</div>';
        
        if (previewType === 'image' && previewUrl) {
            previewContent += '<img src="' + escapeHtml(previewUrl) + '" class="preview-image" alt="Preview">';
        } else if (previewType === 'pdf' && previewUrl) {
            previewContent += '<iframe src="' + escapeHtml(previewUrl) + '" class="preview-iframe"></iframe>';
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
</script>



</body>
<?php include_once 'footer.php'; ?>
