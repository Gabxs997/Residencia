<?php
require_once __DIR__ . '/../../config/conexion.php';

if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.location.href = "altaPartida.php";</script>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.location.href = "altaPartida.php";</script>');
}

// Validar campos obligatorios
$camposObligatorios = ['numeroPartida', 'nombrePartida', 'numeroSubpartida', 'nombreSubpartida'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.location.href = "altaPartida.php";</script>');
    }
}

// Sanitizar datos
$numeroPartida = mysqli_real_escape_string($conectar, $_POST['numeroPartida']);
$nombrePartida = mysqli_real_escape_string($conectar, $_POST['nombrePartida']);
$numeroSubpartida = mysqli_real_escape_string($conectar, $_POST['numeroSubpartida']);
$nombreSubpartida = mysqli_real_escape_string($conectar, $_POST['nombreSubpartida']);

// Verificar si ya existe la misma combinación completa
$verificaExacto = mysqli_query($conectar, "
    SELECT id, eliminado FROM partidas 
    WHERE numero_partida = '$numeroPartida' AND numero_subpartida = '$numeroSubpartida'
    LIMIT 1
");

if (mysqli_num_rows($verificaExacto) > 0) {
    $registro = mysqli_fetch_assoc($verificaExacto);

    // Si está eliminado, lo reactivamos
    if ($registro['eliminado'] == 1) {
        $id = $registro['id'];
        $reactivar = mysqli_query($conectar, "
            UPDATE partidas 
            SET eliminado = 0,
                nombre_partida = '$nombrePartida',
                nombre_subpartida = '$nombreSubpartida'
            WHERE id = $id
        ");

        if ($reactivar) {
            echo '<script>alert("Partida reactivada correctamente."); window.location.href = "../tablas/tablaPartidas.php";</script>';
        } else {
            echo '<script>alert("Error al reactivar la partida: '.str_replace("'", "\\'", mysqli_error($conectar)).'"); window.history.back();</script>';
        }
        exit;
    } else {
        echo '<script>alert("Ya existe una partida con esa combinación de número presupuestal y contable."); window.history.back();</script>';
        exit;
    }
}

// Insertar nueva combinación válida
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
    echo '<script>alert("PARTIDA REGISTRADA CORRECTAMENTE"); window.location.href = "../tablas/tablaPartidas.php";</script>';
} else {
    echo '<script>alert("ERROR AL GUARDAR: '.str_replace("'", "\\'", mysqli_error($conectar)).'"); window.history.back();</script>';
}

mysqli_close($conectar);
?>
