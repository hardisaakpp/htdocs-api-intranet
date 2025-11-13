<?php
include_once "bd_plataforma_web.php";

$buscar = $_GET['buscar'] ?? '';

$sql = "SELECT TOP 500 pro_id, pro_txt_itemcode, pro_txt_descripcion, 
               pro_txt_itemcode_new, log_txt_descripcion_new
        FROM pro_maestro";

$params = [];

if ($buscar !== '') {
    $sql .= " WHERE pro_txt_itemcode LIKE ? OR pro_txt_descripcion LIKE ?";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

$sql .= " ORDER BY pro_id ASC"; // Siempre ordena para evitar errores

$stmt = $db->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_OBJ);
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

    <!-- Buscador -->
    <form method="get" class="row mb-3">
        <div class="col-md-6">
            <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>"
                   class="form-control" placeholder="Ingrese texto para buscar">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
        <div class="col-md-3">
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
                <tr>
                    <td><?= htmlspecialchars($d->pro_txt_itemcode) ?></td>
                    <td><?= htmlspecialchars($d->pro_txt_descripcion) ?></td>
                    <td><?= htmlspecialchars($d->pro_txt_itemcode_new) ?></td>
                    <td><?= htmlspecialchars($d->log_txt_descripcion_new) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                                onclick="abrirModal(
                                    <?= $d->pro_id ?>,
                                    '<?= addslashes($d->pro_txt_itemcode) ?>',
                                    '<?= addslashes($d->pro_txt_descripcion) ?>'
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
    <form method="POST" action="actualizar_pro_maestro.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Producto</h5>
        <button type="button" class="btn-close" onclick="cerrarModal()"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="pro_id" id="modal_pro_id">
        <input type="hidden" name="pro_txt_itemcode" id="modal_itemcode">
        <input type="hidden" name="log_txt_descripcion" id="modal_descripcion">

        <div class="mb-3">
          <label class="form-label">ItemCode Nuevo</label>
          <input type="text" name="pro_txt_itemcode_new" maxlength="10" class="form-control" required
                 inputmode="numeric" pattern="\d*" autocomplete="off"
                 oncopy="return false;" onpaste="return false;" oncut="return false;"
                 onkeydown="return (
                     (event.keyCode >= 48 && event.keyCode <= 57) ||
                     (event.keyCode >= 96 && event.keyCode <= 105) ||
                     event.keyCode === 8 || event.keyCode === 9
                 );">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción Nueva</label>
          <input type="text" name="log_txt_descripcion_new" maxlength="15" class="form-control" required
                 autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;">
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
    $('#tablaProductos').DataTable({
        pageLength: 10,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>
</body>
</html>
