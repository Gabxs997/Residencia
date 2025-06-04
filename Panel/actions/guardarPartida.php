<?php
// 1. Incluir archivo de conexión
require_once __DIR__ . '/../../config/conexion.php';

// 2. Verificar conexión
if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.location.href = "altaPartida.php";</script>');
}

// 3. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.location.href = "altaPartida.php";</script>');
}

// 4. Validar campos obligatorios
$camposObligatorios = ['numeroPartida', 'nombrePartida', 'numeroSubpartida', 'nombreSubpartida'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.location.href = "altaPartida.php";</script>');
    }
}

// 5. Sanitizar datos
$numeroPartida = mysqli_real_escape_string($conectar, $_POST['numeroPartida']);
$nombrePartida = mysqli_real_escape_string($conectar, $_POST['nombrePartida']);
$numeroSubpartida = mysqli_real_escape_string($conectar, $_POST['numeroSubpartida']);
$nombreSubpartida = mysqli_real_escape_string($conectar, $_POST['nombreSubpartida']);

// 6. Verificar si ya existe la partida presupuestal
$verificarPresup = "SELECT id FROM partidas WHERE numero_partida = '$numeroPartida' LIMIT 1";
$resPresup = mysqli_query($conectar, $verificarPresup);
if (mysqli_num_rows($resPresup) > 0) {
    echo '<script>alert("Ya existe una partida presupuestal con ese número."); window.history.back();</script>';
    exit;
}

// 7. Verificar si ya existe la partida contable
$verificarContable = "SELECT id FROM partidas WHERE numero_subpartida = '$numeroSubpartida' LIMIT 1";
$resContable = mysqli_query($conectar, $verificarContable);
if (mysqli_num_rows($resContable) > 0) {
    echo '<script>alert("Ya existe una partida contable con ese número."); window.history.back();</script>';
    exit;
}

// 8. Insertar en la base de datos
$sql = "INSERT INTO partidas (
    numero_partida, 
    nombre_partida, 
    numero_subpartida, 
    nombre_subpartida
) VALUES (
    '$numeroPartida', 
    '$nombrePartida', 
    '$numeroSubpartida', 
    '$nombreSubpartida'
)";

if (mysqli_query($conectar, $sql)) {
    echo '
    <script>
    alert("PARTIDA REGISTRADA CORRECTAMENTE");
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

// 9. Cerrar conexión
mysqli_close($conectar);
?>
