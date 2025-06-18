<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_persona'] ?? '');
    $numeroEmpleado = trim($_POST['numero_empleado'] ?? '');
    $areaId = intval($_POST['area_id'] ?? 0);
    $descripcion = trim($_POST['descripcion_personal'] ?? '');

    if ($nombre !== '' && $numeroEmpleado !== '' && $areaId > 0 && $descripcion !== '') {
        $stmt = mysqli_prepare($conectar, "
            INSERT INTO articulos_personales (nombre_persona, numero_empleado, area_id, descripcion_personal, fecha_registro)
            VALUES (?, ?, ?, ?, NOW())
        ");
        mysqli_stmt_bind_param($stmt, "ssis", $nombre, $numeroEmpleado, $areaId, $descripcion);
        $ejecutado = mysqli_stmt_execute($stmt);

        if ($ejecutado) {
            echo "<script>alert('Artículo personal registrado exitosamente.'); window.location.href = 'articuloPersonal.php';</script>";
        } else {
            echo "<script>alert('Error al registrar el artículo personal.'); history.back();</script>";
        }

        mysqli_stmt_close($stmt);
    } 
} else {
    header("Location: articuloPersonal.php");
    exit;
}
?>
