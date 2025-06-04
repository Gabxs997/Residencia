<?php
require_once __DIR__ . '/../../config/conexion.php';

// Lista blanca de tablas permitidas con sus nombres amigables
$tablasPermitidas = [
    'articulos' => 'Artículo',
    'proveedores' => 'Proveedor',
    'partidas' => 'Partida',
    'ubicaciones' => 'Ubicación'
];

// Obtener parámetros
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validar tabla permitida
if (!array_key_exists($tabla, $tablasPermitidas)) {
    die('<script>alert("Error: Tabla no permitida"); window.history.back();</script>');
}

// Validar ID
if ($id <= 0) {
    die('<script>alert("Error: ID inválido"); window.history.back();</script>');
}

// Obtener nombre amigable para mensajes
$nombreRegistro = $tablasPermitidas[$tabla];

// Consulta de eliminación segura
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