<?php
// 1. Incluir archivo de conexión
require_once __DIR__ . '/../../config/conexion.php';

// Verificar conexión
if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.location.href = "altaUbicacion.php";</script>');
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.location.href = "altaUbicacion.php";</script>');
}

// 3. Validar campos obligatorios
$camposObligatorios = ['nombreArea', 'coordinador', 'rfc', 'telefono'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.location.href = "altaUbicacion.php";</script>');
    }
}

// 4. Sanitizar datos
$nombreArea = mysqli_real_escape_string($conectar, $_POST['nombreArea']);
$coordinador = mysqli_real_escape_string($conectar, $_POST['coordinador']);
$rfc = mysqli_real_escape_string($conectar, $_POST['rfc']);
$telefono = mysqli_real_escape_string($conectar, $_POST['telefono']);

// 5. Preparar la consulta SQL
$sql = "INSERT INTO ubicaciones (
    nombre_area, coordinador, rfc, telefono
) VALUES (
    '$nombreArea', '$coordinador', '$rfc', '$telefono'
)";

// 6. Ejecutar consulta y mostrar resultado
if (mysqli_query($conectar, $sql)) {
    echo '
    <script>
    alert("UBICACIÓN REGISTRADA CORRECTAMENTE");
    window.location.href = "../catalogo.php";
    </script>
    ';
} else {
    echo '
    <script>
    alert("ERROR AL GUARDAR: '.str_replace("'", "\\'", mysqli_error($conectar)).'");
    window.history.back();
    </script>
    ';
}

// 7. Cerrar conexión
mysqli_close($conectar);
?>
