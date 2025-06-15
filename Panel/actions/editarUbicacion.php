<?php
require_once __DIR__ . '/../../config/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>alert('ID inválido'); window.location.href = '../tablas/tablaUbicaciones.php';</script>";
    exit;
}

// Consultar datos actuales
$query = "SELECT * FROM ubicaciones WHERE id = $id";
$result = mysqli_query($conectar, $query);
$ubicacion = mysqli_fetch_assoc($result);

if (!$ubicacion) {
    echo "<script>alert('Ubicación no encontrada'); window.location.href = '../tablas/tablaUbicaciones.php';</script>";
    exit;
}

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conectar, $_POST['nombre_area']);
    $coordinador = mysqli_real_escape_string($conectar, $_POST['coordinador']);
    $rfc = mysqli_real_escape_string($conectar, $_POST['rfc']);
    $telefono = mysqli_real_escape_string($conectar, $_POST['telefono']);

    $sqlUpdate = "UPDATE ubicaciones SET 
                    nombre_area = '$nombre',
                    coordinador = '$coordinador',
                    rfc = '$rfc',
                    telefono = '$telefono'
                  WHERE id = $id";

    if (mysqli_query($conectar, $sqlUpdate)) {
        echo "<script>alert('Ubicación actualizada correctamente'); window.location.href = '../tablas/tablaUbicaciones.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al actualizar la ubicación');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Ubicación</title>
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<div class="modal-overlay">
    <div class="modal-container">
        <span class="modal-close" onclick="window.location.href='../tablas/tablaUbicaciones.php'">
            <i class="fas fa-times"></i>
        </span>

        <h2 class="modal-title">Editar Ubicación ID: <?= $ubicacion['id'] ?></h2>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre_area">Área</label>
                    <input type="text" id="nombre_area" name="nombre_area" value="<?= htmlspecialchars($ubicacion['nombre_area']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="coordinador">Coordinador</label>
                    <input type="text" id="coordinador" name="coordinador" value="<?= htmlspecialchars($ubicacion['coordinador']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="rfc">RFC</label>
                    <input type="text" id="rfc" name="rfc" value="<?= htmlspecialchars($ubicacion['rfc']) ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($ubicacion['telefono']) ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="window.location.href='../tablas/tablaUbicaciones.php'">Cancelar</button>
                <button type="submit" class="btn btn-save">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

