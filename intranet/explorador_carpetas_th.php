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
    .folder-icon, .file-icon {
        width: 24px;
        margin-right: 8px;
    }
    .breadcrumb {
        background-color: #f8f9fa;
    }
    form.search-form {
        max-width: 400px;
        margin-bottom: 20px;
    }
    .folder-link {
        cursor: pointer;
        color: #007bff;
        text-decoration: none;
    }
    .folder-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }
    .folder-row:hover {
        background-color: #f8f9fa;
    }
    .file-preview-trigger {
        position: relative;
        cursor: pointer;
    }
    .file-preview {
        position: fixed;
        background: white;
        border: 2px solid #007bff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
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
        color: #333;
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
    <h3>📁 Explorador de Carpetas - TH</h3>

    <!-- Formulario de búsqueda -->
    <form class="search-form d-flex" method="get">
        <input type="text" name="search" class="form-control me-2" placeholder="Buscar archivo o carpeta..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        <?php if ($search): ?>
            <a href="explorador_carpetas_th.php" class="btn btn-outline-danger ms-2">Limpiar</a>
        <?php endif; ?>
    </form>

    <!-- Breadcrumb -->
    <?php if (!$search): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?">Raíz</a></li>
                <?php
                $pathParts = explode('/', trim($relPath, '/'));
                $pathSoFar = '';
                foreach ($pathParts as $part) {
                    if ($part === '') continue;
                    $pathSoFar .= $part . '/';
                    echo '<li class="breadcrumb-item"><a href="?path=' . urlencode($pathSoFar) . '">' . htmlspecialchars($part) . '</a></li>';
                }
                ?>
            </ol>
        </nav>
    <?php endif; ?>

    <!-- Tabla de resultados -->
    <table class="table table-hover table-sm">
        <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Ruta</th>
            <th>Tipo</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="3" class="text-center text-muted">No se encontraron resultados.</td></tr>
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
                                    <span style="cursor:pointer; color:#007bff; text-decoration:underline;"
                                        onclick="event.stopPropagation(); openPDFModal('ver_pdf_th.php?file=<?php echo urlencode($item['ruta_relativa']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>')">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </span>
                                <?php else: ?>
                                    <span><?php echo htmlspecialchars($item['nombre']); ?></span>
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

<!-- Modal Visor PDF -->
<!-- Preview Tooltip -->
<div id="filePreview" class="file-preview"></div>

<div id="pdfModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.8); z-index:1000;">
  <div style="position:relative; width:90%; height:90%; margin:5% auto; background:#fff; border-radius:8px; overflow:hidden;">
    <div style="display:flex; justify-content:space-between; align-items:center; background:#343a40; color:#fff; padding:10px;">
      <span id="modalFilename" style="font-weight:bold;"></span>
      <div>
        <button onclick="printPDF()" class="btn btn-sm btn-light me-2">🖨 Imprimir</button>
        <a id="downloadLink" class="btn btn-sm btn-light me-2" download target="_blank">⬇ Descargar</a>
        <button onclick="closeModal()" class="btn btn-sm btn-danger">✖ Cerrar</button>
      </div>
    </div>
    <iframe id="pdfViewer" style="width:100%; height:100%;" frameborder="0"></iframe>
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
