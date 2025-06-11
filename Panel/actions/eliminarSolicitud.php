<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitud_id'])) {
    $id = intval($_POST['solicitud_id']);

    // No se elimina, solo se marca como no visible
    $resultado = mysqli_query($conectar, "UPDATE solicitudes_mantenimiento SET visible = 0 WHERE id = $id");

    if ($resultado) {
        header("Location: ../reportes/mantenimiento.php?msg=eliminado");
    } else {
        header("Location: ../reportes/mantenimiento.php?msg=error");
    }
    exit;
}
?>
