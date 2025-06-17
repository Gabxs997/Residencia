<?php
require_once __DIR__ . '/../../config/conexion.php';

// 🔒 Eliminar usuario de departamento (eliminación física)
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

// ✅ Lógica especial para ubicaciones (verifica artículos Y usuarios)
if ($tabla === 'ubicaciones') {
    $checkArticulos = mysqli_query($conectar, "SELECT COUNT(*) as total FROM articulos WHERE ubicacion = $id");
    $checkUsuarios = mysqli_query($conectar, "SELECT COUNT(*) as total FROM usuarios_departamento WHERE area_id = $id");

    $articulos = mysqli_fetch_assoc($checkArticulos)['total'];
    $usuarios = mysqli_fetch_assoc($checkUsuarios)['total'];

    if ($articulos > 0 || $usuarios > 0) {
        // Si tiene artículos o usuarios: soft delete
        $query = "UPDATE ubicaciones SET eliminado = 1 WHERE id = $id";
    } else {
        // Eliminación física si no hay vínculos
        $query = "DELETE FROM ubicaciones WHERE id = $id";
    }
} elseif (in_array($tabla, ['proveedores', 'partidas'])) {
    // Soft delete para proveedores y partidas
    $query = "UPDATE $tabla SET eliminado = 1 WHERE id = $id";
} else {
    // Eliminación física para otras tablas como artículos
    $query = "DELETE FROM $tabla WHERE id = $id";
}

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
