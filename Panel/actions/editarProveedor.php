<?php
require_once __DIR__ . '/../../config/conexion.php';

// Obtener ID del proveedor
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del proveedor
$query = "SELECT p.*, c.nombre AS contacto_nombre, c.telefono1 AS contacto_telefono, c.email AS contacto_email, c.estado AS contacto_estado 
          FROM proveedores p
          LEFT JOIN contactos_proveedores c ON p.id = c.proveedor_id
          WHERE p.id = $id";
$result = mysqli_query($conectar, $query);
$proveedor = mysqli_fetch_assoc($result);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon_social = $proveedor['razon_social']; // No permitir edición
    $direccion = mysqli_real_escape_string($conectar, $_POST['direccion']);
    $telefono1 = mysqli_real_escape_string($conectar, $_POST['telefono1']);
    $telefono2 = mysqli_real_escape_string($conectar, $_POST['telefono2']);
    $email = mysqli_real_escape_string($conectar, $_POST['email']);
    $estado = mysqli_real_escape_string($conectar, $_POST['estado']);
    $contacto_nombre = mysqli_real_escape_string($conectar, $_POST['contacto_nombre']);
    $contacto_telefono = mysqli_real_escape_string($conectar, $_POST['contacto_telefono']);
    $contacto_email = mysqli_real_escape_string($conectar, $_POST['contacto_email']);
    $contacto_estado = mysqli_real_escape_string($conectar, $_POST['contacto_estado']);

    // Actualizar proveedor
    $sqlProveedor = "UPDATE proveedores SET 
        direccion = '$direccion', 
        telefono1 = '$telefono1', 
        telefono2 = '$telefono2', 
        email = '$email', 
        estado = '$estado' 
        WHERE id = $id";

    // Verificar si ya tiene contacto relacionado
    $checkContacto = "SELECT id FROM contactos_proveedores WHERE proveedor_id = $id";
    $resCheck = mysqli_query($conectar, $checkContacto);

    if (mysqli_num_rows($resCheck) > 0) {
        // Actualizar contacto
        $sqlContacto = "UPDATE contactos_proveedores SET 
            nombre = '$contacto_nombre', 
            telefono1 = '$contacto_telefono', 
            email = '$contacto_email', 
            estado = '$contacto_estado' 
            WHERE proveedor_id = $id";
    } else {
        // Insertar contacto nuevo
        $sqlContacto = "INSERT INTO contactos_proveedores (proveedor_id, nombre, telefono1, email, estado)
                        VALUES ($id, '$contacto_nombre', '$contacto_telefono', '$contacto_email', '$contacto_estado')";
    }

    if (mysqli_query($conectar, $sqlProveedor) && mysqli_query($conectar, $sqlContacto)) {
        echo "<script>alert('Proveedor actualizado correctamente'); window.location.href = '../tablas/tablaProveedores.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar proveedor');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<div class="modal-overlay">
    <div class="modal-container">
        <span class="modal-close" onclick="window.location.href='../tablas/tablaProveedores.php'">
            <i class="fas fa-times"></i>
        </span>
        <h2 class="modal-title">Editar Proveedor ID: <?= $proveedor['id'] ?></h2>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="razon_social">Razón Social</label>
                    <input type="text" name="razon_social" id="razon_social" value="<?= htmlspecialchars($proveedor['razon_social']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($proveedor['direccion']) ?>">
                </div>
                <div class="form-group">
                    <label for="telefono1">Teléfono 1</label>
                    <input type="tel" name="telefono1" id="telefono1" value="<?= htmlspecialchars($proveedor['telefono1']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono2">Teléfono 2</label>
                    <input type="tel" name="telefono2" id="telefono2" value="<?= htmlspecialchars($proveedor['telefono2']) ?>">
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($proveedor['email']) ?>">
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" required>
                        <option value="Activo" <?= $proveedor['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $proveedor['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="contacto_nombre">Nombre de Contacto</label>
                    <input type="text" name="contacto_nombre" id="contacto_nombre" value="<?= htmlspecialchars($proveedor['contacto_nombre']) ?>">
                </div>
                <div class="form-group">
                    <label for="contacto_telefono">Teléfono de Contacto</label>
                    <input type="tel" name="contacto_telefono" id="contacto_telefono" value="<?= htmlspecialchars($proveedor['contacto_telefono']) ?>">
                </div>
                <div class="form-group">
                    <label for="contacto_email">Email de Contacto</label>
                    <input type="email" name="contacto_email" id="contacto_email" value="<?= htmlspecialchars($proveedor['contacto_email']) ?>">
                </div>
                <div class="form-group">
                    <label for="contacto_estado">Estado del Contacto</label>
                    <select name="contacto_estado" id="contacto_estado">
                        <option value="Activo" <?= $proveedor['contacto_estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $proveedor['contacto_estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="window.location.href='../tablas/tablaProveedores.php'">Cancelar</button>
                <button type="submit" class="btn btn-save">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
