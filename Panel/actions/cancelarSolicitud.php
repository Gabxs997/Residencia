<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitud_id'])) {
    $solicitud_id = intval($_POST['solicitud_id']);

    $resultado = mysqli_query($conectar, "UPDATE solicitudes_mantenimiento SET estatus = 3 WHERE id = $solicitud_id");

    // Detectar si es AJAX
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        echo json_encode(['success' => $resultado]);
    } else {
        // Redirige de forma clásica si no es AJAX
        header("Location: ../reportesT/solicitarMantenimiento.php");
        exit;
    }
}
?>
