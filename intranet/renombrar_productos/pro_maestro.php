<?php
include_once "bd_plataforma_web.php";

$buscar = $_GET['buscar'] ?? '';
$filtro_vacios = $_GET['filtro_vacios'] ?? '';

$sql = "SELECT TOP 5000 
            pro_id, 
            pro_txt_itemcode, 
            pro_txt_descripcion, 
            pro_txt_itemcode_new, 
            log_txt_descripcion_new
        FROM pro_maestro
        WHERE 1=1";
$params = [];

if ($buscar !== '') {
    $sql .= " AND (pro_txt_itemcode LIKE ? OR pro_txt_descripcion LIKE ?)";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

if ($filtro_vacios === 'vacios') {
    $sql .= " AND (pro_txt_itemcode_new IS NULL OR pro_txt_itemcode_new = '')";
}

$sql .= " ORDER BY pro_id ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_OBJ);

function limpiarTextoJS($texto) {
    // Quita comillas dobles y simples para no romper el HTML/JS
    return str_replace(['"', "'"], '', $texto);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Maestro de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4 text-center">Maestro de Productos</h3>

    <!-- Filtros -->
    <form method="get" class="row mb-3">
        <div class="col-md-4">
            <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>"
                   class="form-control" placeholder="Buscar producto...">
        </div>
        <div class="col-md-3">
            <select name="filtro_vacios" class="form-select">
                <option value="">Todos</option>
                <option value="vacios" <?= $filtro_vacios === 'vacios' ? 'selected' : '' ?>>No modificados</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
        <div class="col-md-2">
            <a href="pro_maestro.php" class="btn btn-secondary w-100">Limpiar</a>
        </div>
    </form>

    <div class="table-responsive">
        <table id="tablaProductos" class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>ItemCode</th>
                    <th>Descripción</th>
                    <th>ItemCode Nuevo</th>
                    <th>Descripción Nueva</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $d): ?>
                <tr data-id="<?= (int)$d->pro_id ?>">
                    <td><?= htmlspecialchars($d->pro_txt_itemcode ?? '') ?></td>
                    <td><?= htmlspecialchars($d->pro_txt_descripcion ?? '') ?></td>
                    <td><?= htmlspecialchars($d->pro_txt_itemcode_new ?? '') ?></td>
                    <td><?= htmlspecialchars($d->log_txt_descripcion_new ?? '') ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                            onclick="abrirModal(
                                <?= (int)$d->pro_id ?>,
                                '<?= limpiarTextoJS($d->pro_txt_itemcode) ?>',
                                '<?= limpiarTextoJS($d->pro_txt_descripcion) ?>'
                            )">
                            Editar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditar" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Producto</h5>
        <button type="button" class="btn-close" onclick="cerrarModal()"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="pro_id" id="modal_pro_id">
        <input type="hidden" name="pro_txt_itemcode" id="modal_itemcode">
        <input type="hidden" name="log_txt_descripcion" id="modal_descripcion">

        <div class="mb-3">
          <label class="form-label">ItemCode Nuevo (solo números, máx 10)</label>
          <input type="text" name="pro_txt_itemcode_new" maxlength="10" class="form-control" required
                 inputmode="numeric" pattern="\d*" autocomplete="off"
                 onkeypress="return /[0-9]/.test(event.key)"
                 onpaste="return false;" oncopy="return false;" oncut="return false;">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción Nueva (alfanumérica, máx 15)</label>
          <input type="text" name="log_txt_descripcion_new" maxlength="15" autocomplete="off" class="form-control" required
                 onkeypress="return /^[A-Za-z0-9 ]$/.test(event.key)"
                 onpaste="return false;" oncopy="return false;" oncut="return false;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
function abrirModal(id, itemcode, descripcion) {
 // Limpiar primero
    $('#modal_pro_id').val('');
    $('#modal_itemcode').val('');
    $('#modal_descripcion').val('');
    $('input[name="pro_txt_itemcode_new"]').val('');
    $('input[name="log_txt_descripcion_new"]').val('');

    $('#modal_pro_id').val(id);
    $('#modal_itemcode').val(itemcode);
    $('#modal_descripcion').val(descripcion);
    let modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}
function cerrarModal() {
    let modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
    modal.hide();
}

$(document).ready(function () {
    let tabla = $('#tablaProductos').DataTable({
        pageLength: 10,
        searching: false,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // Guardar por AJAX
    $('#formEditar').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'actualizar_pro_maestro.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === "ok") {
                    let row = $('#tablaProductos tbody tr[data-id="'+res.pro_id+'"]');
                    row.find('td:eq(2)').text(res.pro_txt_itemcode_new);
                    row.find('td:eq(3)').text(res.log_txt_descripcion_new);
                    cerrarModal();
                } else {
                    alert("Error: " + (res.message || "No se guardó"));
                }
            },
            error: function () {
                alert("Error al guardar.");
            }
        });
    });
});
</script>
</body>
</html>
