<?php
$contraseña = "Datos.22";
$usuario = "consultas";
$nombreBaseDeDatos = "plataforma_web";
$rutaServidor = "10.10.100.12";

try {
    $db = new PDO("sqlsrv:server=$rutaServidor;TrustServerCertificate=TRUE;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Error al conectar con plataforma_web: " . $e->getMessage();
}
?>
