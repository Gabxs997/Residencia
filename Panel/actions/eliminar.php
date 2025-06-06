<?php
require_once __DIR__ . '/../../config/conexion.php';

// 🔒 Eliminar usuario de departamento (antes de cualquier otra lógica)
if (isset($_GET['eliminar_usuario'])) {
    $id = intval($_GET['eliminar_usuario']);

    $eliminar = mysqli_query($conectar, "DELETE FROM usuarios_departamento WHERE id = $id");

    if ($eliminar) {
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href = '../crearUsuario.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el usuario.'); window.location.href = '../crearUsuario.php';</script>";
    }
    exit;
}

// 🔒 Eliminar de otras tablas
$tablasPermitidas = [
    'articulos' => 'Artículo',
    'proveedores' => 'Proveedor',
    'partidas' => 'Partida',
    'ubicaciones' => 'Ubicación'
];

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!array_key_exists($tabla, $tablasPermitidas)) {
    die('<script>alert("Error: Tabla no permitida"); window.history.back();</script>');
}

if ($id <= 0) {
    die('<script>alert("Error: ID inválido"); window.history.back();</script>');
}

$nombreRegistro = $tablasPermitidas[$tabla];

$query = "DELETE FROM " . mysqli_real_escape_string($conectar, $tabla) . " WHERE id = $id";
$resultado = mysqli_query($conectar, $query);

if ($resultado && mysqli_affected_rows($conectar) > 0) {
    echo '<script>
          alert("' . $nombreRegistro . ' eliminado correctamente");
          window.location.href = "../tablas/tabla' . ucfirst($tabla) . '.php";
          </script>';
} else {
    echo '<script>
          alert("Error al eliminar ' . strtolower($nombreRegistro) . ': ' . addslashes(mysqli_error($conectar)) . '");
          window.history.back();
          </script>';
}

mysqli_close($conectar);
?>
