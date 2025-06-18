<?php
require_once __DIR__ . '/../../config/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: articuloPersonal.php');
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conectar, "SELECT * FROM articulos_personales WHERE id = $id");
$articulo = mysqli_fetch_assoc($query);

if (!$articulo) {
    echo "<script>alert('Artículo no encontrado.'); window.location.href = 'articuloPersonal.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_persona']);
    $numeroEmpleado = trim($_POST['numero_empleado']);
    $areaId = intval($_POST['area_id']);
    $descripcion = trim($_POST['descripcion_personal']);

    $stmt = mysqli_prepare($conectar, "
        UPDATE articulos_personales 
        SET nombre_persona = ?, numero_empleado = ?, area_id = ?, descripcion_personal = ?
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "ssisi", $nombre, $numeroEmpleado, $areaId, $descripcion, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>alert('Artículo actualizado correctamente.'); window.location.href = 'articuloPersonal.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Artículo Personal</title>
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
    
   
</head>
<body>
    <div class="modal-overlay" style="display:flex;">
        <div class="modal-container">
            <h2 class="modal-title">Editar Artículo Personal</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre_persona">Nombre:</label>
                        <input type="text" name="nombre_persona" value="<?= htmlspecialchars($articulo['nombre_persona']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="numero_empleado">Número de empleado:</label>
                        <input type="text" name="numero_empleado" value="<?= htmlspecialchars($articulo['numero_empleado']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="area_id">Área:</label>
                        <select name="area_id" required>
                            <option disabled>Seleccione un área</option>
                            <?php
                            $areas = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area");
                            while ($area = mysqli_fetch_assoc($areas)) {
                                $selected = $articulo['area_id'] == $area['id'] ? 'selected' : '';
                                echo "<option value='{$area['id']}' $selected>" . htmlspecialchars($area['nombre_area']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label for="descripcion_personal">Descripción:</label>
                        <textarea name="descripcion_personal" rows="3" required><?= htmlspecialchars($articulo['descripcion_personal']) ?></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-save">Actualizar</button>
                    <a href="articuloPersonal.php" class="btn btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
