<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['articulo_id'])) {
    $articulo_id = intval($_POST['articulo_id']);
    $area_id = isset($_GET['area_id']) ? intval($_GET['area_id']) : 0;

    // Capturar nombre del solicitante y descripción del problema
    $nombre = mysqli_real_escape_string($conectar, $_POST['nombre_solicitante'] ?? '');
    $problema = mysqli_real_escape_string($conectar, $_POST['descripcion_problema'] ?? '');

    // Validar que no exista ya una solicitud activa
    $check = mysqli_query($conectar, "
        SELECT id FROM solicitudes_mantenimiento
        WHERE articulo_id = $articulo_id AND estatus IN (0, 1)
    ");

    if (mysqli_num_rows($check) === 0) {
        // Insertar nueva solicitud
        $insert = mysqli_query($conectar, "
            INSERT INTO solicitudes_mantenimiento (
                articulo_id, nombre_solicitante, descripcion_problema, fecha_solicitud, estatus
            ) VALUES (
                $articulo_id, '$nombre', '$problema', CURDATE(), 0
            )
        ");

        if ($insert) {
            echo "<script>alert('Solicitud registrada exitosamente.'); window.location.href = '../reportesT/solicitarMantenimiento.php?area_id=$area_id';</script>";
        } else {
            echo "<script>alert('Error al registrar la solicitud.'); window.location.href = '../reportesT/solicitarMantenimiento.php?area_id=$area_id';</script>";
        }
    } else {
        echo "<script>alert('Ya existe una solicitud activa para este artículo.'); window.location.href = '../reportesT/solicitarMantenimiento.php?area_id=$area_id';</script>";
    }
    exit;
}
?>
