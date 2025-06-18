<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $delete = mysqli_query($conectar, "DELETE FROM articulos_personales WHERE id = $id");

    if ($delete) {
        echo "<script>alert('Artículo eliminado correctamente.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el artículo.');</script>";
    }
}

echo "<script>window.location.href = 'articuloPersonal.php';</script>";
exit;
?>
