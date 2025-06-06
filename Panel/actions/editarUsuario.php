<?php
require_once __DIR__ . '/../../config/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM usuarios_departamento WHERE id = $id";
$result = mysqli_query($conectar, $query);
$usuario = mysqli_fetch_assoc($result);

// Procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUsuario = mysqli_real_escape_string($conectar, $_POST['usuario']);
    $areaId = intval($_POST['area_id']);
    $nuevaContrasena = $_POST['password'];

    $updateQuery = "UPDATE usuarios_departamento SET usuario = '$nuevoUsuario', area_id = $areaId";

    if (!empty($nuevaContrasena)) {
        $hash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $updateQuery .= ", contrasena = '$hash'";
    }

    $updateQuery .= " WHERE id = $id";

    if (mysqli_query($conectar, $updateQuery)) {
        echo "<script>alert('Usuario actualizado correctamente'); window.location.href = '../crearUsuario.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="modal-overlay" style="display:flex;">
        <div class="modal-container">
            <span class="modal-close" onclick="window.location.href='../crearUsuario.php'"><i class="fas fa-times"></i></span>
            <h2 class="modal-title">Editar Usuario</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="usuario">Nombre de usuario:</label>
                        <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Nueva contraseña:</label>
                        <input type="password" name="password" placeholder="Crear nueva contraseña">
                    </div>

                    <div class="form-group">
                        <label for="area_id">Área (Departamento):</label>
                        <select name="area_id" required>
                            <option value="">Seleccione</option>
                            <?php
                            $areas = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area");
                            while ($a = mysqli_fetch_assoc($areas)) {
                                $selected = $a['id'] == $usuario['area_id'] ? 'selected' : '';
                                echo "<option value='{$a['id']}' $selected>" . htmlspecialchars($a['nombre_area']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-save">Guardar cambios</button>
                    <button type="button" class="btn btn-cancel" onclick="window.location.href='../crearUsuario.php'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
