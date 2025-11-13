<?php
include_once "bd_plataforma_web.php";

if (
    isset($_POST['pro_id'], $_POST['pro_txt_itemcode'], $_POST['log_txt_descripcion'],
          $_POST['pro_txt_itemcode_new'], $_POST['log_txt_descripcion_new'])
) {
    $pro_id = $_POST['pro_id'];
    $itemcode = $_POST['pro_txt_itemcode'];
    $descripcion = $_POST['log_txt_descripcion'];
    $itemcode_new = $_POST['pro_txt_itemcode_new'];
    $descripcion_new = $_POST['log_txt_descripcion_new'];
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        // 🔎 Validar si el código nuevo ya existe en otra fila
        $sqlCheck = "SELECT COUNT(*) AS total FROM pro_maestro 
                     WHERE pro_txt_itemcode_new = ? AND pro_id != ?";
        $stmt = $db->prepare($sqlCheck);
        $stmt->execute([$itemcode_new, $pro_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] > 0) {
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "CÓDIGO NUEVO DUPLICADO: Ya existe en otro registro."
            ]);
            exit;
        }

        // 1. Update estado anterior en pro_log
        $sqlUpdate = "UPDATE pro_log SET log_sts_estado = 0 
                      WHERE pro_txt_itemcode = ? AND log_txt_descripcion = ?";
        $stmt = $db->prepare($sqlUpdate);
        $stmt->execute([$itemcode, $descripcion]);

        // 2. Insert nuevo registro en pro_log
        $sqlInsert = "INSERT INTO pro_log (
                          pro_txt_itemcode, pro_txt_itemcode_new, 
                          log_txt_descripcion, log_txt_descripcion_new, 
                          log_txt_ip, log_sts_estado
                      ) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $db->prepare($sqlInsert);
        $stmt->execute([$itemcode, $itemcode_new, $descripcion, $descripcion_new, $ip]);

        // 3. Update en pro_maestro
        $sqlMaestro = "UPDATE pro_maestro 
                       SET pro_txt_itemcode_new = ?, log_txt_descripcion_new = ? 
                       WHERE pro_id = ?";
        $stmt = $db->prepare($sqlMaestro);
        $stmt->execute([$itemcode_new, $descripcion_new, $pro_id]);

        // ✅ Respuesta JSON
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "ok",
            "pro_id" => $pro_id,
            "pro_txt_itemcode_new" => $itemcode_new,
            "log_txt_descripcion_new" => $descripcion_new
        ]);
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Datos incompletos."]);
    exit;
}
