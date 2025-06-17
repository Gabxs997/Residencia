<?php
require_once __DIR__ . '/../../config/conexion.php';

if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.location.href = "altaUbicacion.php";</script>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.location.href = "altaUbicacion.php";</script>');
}

$camposObligatorios = ['nombreArea', 'coordinador', 'rfc', 'telefono'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.location.href = "altaUbicacion.php";</script>');
    }
}

$nombreArea = strtoupper(trim(mysqli_real_escape_string($conectar, $_POST['nombreArea'])));
$coordinador = mysqli_real_escape_string($conectar, $_POST['coordinador']);
$rfc = mysqli_real_escape_string($conectar, $_POST['rfc']);
$telefono = mysqli_real_escape_string($conectar, $_POST['telefono']);

// 🔍 Buscar áreas parecidas
$similares = [];
$consulta = mysqli_query($conectar, "SELECT id, nombre_area, eliminado FROM ubicaciones");

while ($row = mysqli_fetch_assoc($consulta)) {
    similar_text($row['nombre_area'], $nombreArea, $porcentaje);
    if ($porcentaje >= 85) {
        $similares[] = $row;
    }
}

// Si hay áreas similares
if (!empty($similares)) {
    // Tomar la coincidencia más cercana
    $másParecido = $similares[0]; // el primero con % >= 85
    $id = $másParecido['id'];

    // Actualizar sus datos y reactivar si estaba eliminado
    $actualizar = mysqli_query($conectar, "
        UPDATE ubicaciones SET 
            nombre_area = '$nombreArea', 
            coordinador = '$coordinador', 
            rfc = '$rfc', 
            telefono = '$telefono', 
            eliminado = 0 
        WHERE id = $id
    ");

    if ($actualizar) {
        echo "<script>alert('Se detectó un área similar. Se actualizó y reactivó como \"$nombreArea\".'); window.location.href = '../tablas/tablaUbicaciones.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el área similar.'); window.history.back();</script>";
    }
    exit;
}


// 🔁 Verificar si existe eliminada y reactivar
$verificar = mysqli_query($conectar, "SELECT id FROM ubicaciones WHERE nombre_area = '$nombreArea' AND eliminado = 1");
if (mysqli_num_rows($verificar) > 0) {
    $row = mysqli_fetch_assoc($verificar);
    $id = $row['id'];
    $reactivar = mysqli_query($conectar, "
        UPDATE ubicaciones SET eliminado = 0, coordinador = '$coordinador', rfc = '$rfc', telefono = '$telefono' WHERE id = $id
    ");
    if ($reactivar) {
        echo '<script>alert("Ubicación reactivada correctamente."); window.location.href = "../catalogo.php";</script>';
    } else {
        echo '<script>alert("Error al reactivar la ubicación."); window.history.back();</script>';
    }
    exit;
}

// ✅ Insertar si todo está bien
$sql = "INSERT INTO ubicaciones (nombre_area, coordinador, rfc, telefono)
        VALUES ('$nombreArea', '$coordinador', '$rfc', '$telefono')";

if (mysqli_query($conectar, $sql)) {
    echo '<script>alert("UBICACIÓN REGISTRADA CORRECTAMENTE"); window.location.href = "../tablas/tablaUbicaciones.php";</script>';
} else {
    echo '<script>alert("ERROR AL GUARDAR: '.str_replace("'", "\\'", mysqli_error($conectar)).'"); window.history.back();</script>';
}

mysqli_close($conectar);
?>
