<?php
require_once __DIR__ . '/../../config/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta para obtener el artículo junto con el RFC del coordinador del área
$query = "SELECT a.*, u.rfc AS rfc_responsable_area
          FROM articulos a
          LEFT JOIN ubicaciones u ON a.ubicacion = u.id
          WHERE a.id = $id";
$result = mysqli_query($conectar, $query);
$articulo = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = array_map(function ($value) use ($conectar) {
        return mysqli_real_escape_string($conectar, $value);
    }, $_POST);

    // Validar si el número de inventario ya existe en otro artículo
   $noInventario = trim($datos['no_inventario']);

// Solo si no_inventario está lleno, validamos
if (!empty($noInventario)) {
    $checkQuery = "SELECT id FROM articulos WHERE no_inventario = '$noInventario' AND id != $id LIMIT 1";
    $checkResult = mysqli_query($conectar, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Ya existe otro artículo con ese número de inventario.'); window.location.href = window.location.href;</script>";
        exit;
    }
}

    $updateQuery = "UPDATE articulos SET 
        ur = '{$datos['ur']}',
        no_inventario = '{$datos['no_inventario']}',
        cabm = '{$datos['cabm']}',
        descripcion = '{$datos['descripcion']}',
        descripcion_detalle = '{$datos['descripcion_detalle']}',
        partida_presupuestal = '{$datos['partida_presupuestal']}',
        partida_contable = '{$datos['partida_contable']}',
        fecha_alta = '{$datos['fecha_alta']}',
        fecha_documento = '{$datos['fecha_documento']}',
        tipo_bien = '{$datos['tipo_bien']}',
        no_contrato = '{$datos['no_contrato']}',
        no_factura = '{$datos['no_factura']}',
        proveedor_id = '{$datos['proveedor_id']}',
        serie = '{$datos['serie']}',
        modelo = '{$datos['modelo']}',
        marca = '{$datos['marca']}',
        estado_bien = '{$datos['estado_bien']}',
        ubicacion = '{$datos['ubicacion']}',
        importe = '{$datos['importe']}',
        observaciones = '{$datos['observaciones']}',
        origen = '{$datos['origen']}'
        WHERE id = $id";

    if (mysqli_query($conectar, $updateQuery)) {
        echo "<script>alert('Artículo actualizado correctamente'); window.location.href = '../tablas/tablaArticulos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el artículo');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Artículo</title>
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="modal-overlay">
        <div class="modal-container">
            <span class="modal-close" onclick="window.location.href='../tablas/tablaArticulos.php'">
                <i class="fas fa-times"></i>
            </span>

            <h2 class="modal-title">Editar Artículo ID: <?= $articulo['id'] ?></h2>

            <form method="POST" action="">
                <div class="form-grid">
                    <!-- Campo por campo -->
                    <div class="form-group"><label>UR</label><input type="text" name="ur" value="<?= $articulo['ur'] ?>" required></div>
                    <div class="form-group"><label>No. Inventario</label><input type="text" name="no_inventario" value="<?= $articulo['no_inventario'] ?>" required></div>
                    <div class="form-group"><label>CABM</label><input type="text" name="cabm" value="<?= $articulo['cabm'] ?>"></div>
                    <div class="form-group"><label>Descripción</label><input type="text" name="descripcion" value="<?= $articulo['descripcion'] ?>" required></div>
                    <div class="form-group"><label>Desc. Detalle</label><input type="text" name="descripcion_detalle" value="<?= $articulo['descripcion_detalle'] ?>"></div>

                    <!-- Partida Presupuestal -->
                    <div class="form-group">
                        <label>Partida Presupuestal</label>
                        <select name="partida_presupuestal" required>
                            <option value="">Selecciona una</option>
                            <?php
                            $presup = mysqli_query($conectar, "SELECT DISTINCT numero_partida FROM partidas ORDER BY numero_partida ASC");
                            while ($p = mysqli_fetch_assoc($presup)) {
                                $selected = $p['numero_partida'] == $articulo['partida_presupuestal'] ? 'selected' : '';
                                echo "<option value='{$p['numero_partida']}' $selected>{$p['numero_partida']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Partida Contable -->
                    <div class="form-group">
                        <label>Partida Contable</label>
                        <select name="partida_contable" required>
                            <option value="">Selecciona una</option>
                            <?php
                            $cont = mysqli_query($conectar, "SELECT DISTINCT numero_subpartida FROM partidas ORDER BY numero_subpartida ASC");
                            while ($c = mysqli_fetch_assoc($cont)) {
                                $selected = $c['numero_subpartida'] == $articulo['partida_contable'] ? 'selected' : '';
                                echo "<option value='{$c['numero_subpartida']}' $selected>{$c['numero_subpartida']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group"><label>F. Alta</label><input type="date" name="fecha_alta" value="<?= $articulo['fecha_alta'] ?>"></div>
                    <div class="form-group"><label>F. Documento</label><input type="date" name="fecha_documento" value="<?= $articulo['fecha_documento'] ?>"></div>
                    <div class="form-group"><label>Tipo Bien</label><input type="text" name="tipo_bien" value="<?= $articulo['tipo_bien'] ?>"></div>
                    <div class="form-group"><label>No. Contrato</label><input type="text" name="no_contrato" value="<?= $articulo['no_contrato'] ?>"></div>
                    <div class="form-group"><label>No. Factura</label><input type="text" name="no_factura" value="<?= $articulo['no_factura'] ?>"></div>

                    <!-- Proveedor -->
                    <div class="form-group">
                        <label>Proveedor</label>
                        <select name="proveedor_id" required>
                            <option value="">Selecciona un proveedor</option>
                            <?php
                            $proveedores = mysqli_query($conectar, "SELECT id, razon_social FROM proveedores WHERE estado = 'Activo'");
                            while ($prov = mysqli_fetch_assoc($proveedores)) {
                                $selected = $prov['id'] == $articulo['proveedor_id'] ? 'selected' : '';
                                echo "<option value='{$prov['id']}' $selected>{$prov['razon_social']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group"><label>Serie</label><input type="text" name="serie" value="<?= $articulo['serie'] ?>"></div>
                    <div class="form-group"><label>Modelo</label><input type="text" name="modelo" value="<?= $articulo['modelo'] ?>"></div>
                    <div class="form-group"><label>Marca</label><input type="text" name="marca" value="<?= $articulo['marca'] ?>"></div>

                    <!-- Estado del Bien -->
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado_bien" required>
                            <option value="Bueno" <?= $articulo['estado_bien'] == 'Bueno' ? 'selected' : '' ?>>Bueno</option>
                            <option value="Regular" <?= $articulo['estado_bien'] == 'Regular' ? 'selected' : '' ?>>Regular</option>
                            <option value="Malo" <?= $articulo['estado_bien'] == 'Malo' ? 'selected' : '' ?>>Malo</option>
                        </select>
                    </div>

                    <!-- Ubicación -->
                    <div class="form-group">
                        <label>Ubicación (Área)</label>
                        <select name="ubicacion" required>
                            <option value="">Selecciona un área</option>
                            <?php
                            $areas = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area ASC");
                            while ($a = mysqli_fetch_assoc($areas)) {
                                $selected = $a['id'] == $articulo['ubicacion'] ? 'selected' : '';
                                echo "<option value='{$a['id']}' $selected>{$a['nombre_area']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group"><label>RFC del Responsable</label><input type="text" name="rfc_responsable" value="<?= htmlspecialchars($articulo['rfc_responsable_area']) ?>" readonly></div>
                    <div class="form-group"><label>Importe</label><input type="number" step="0.01" name="importe" value="<?= $articulo['importe'] ?>"></div>
                    <div class="form-group full-width"><label>Observaciones</label><textarea name="observaciones"><?= $articulo['observaciones'] ?></textarea></div>
                    <div class="form-group"><label>Origen</label><input type="text" name="origen" value="<?= $articulo['origen'] ?>"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="window.location.href='../tablas/tablaArticulos.php'">Cancelar</button>
                    <button type="submit" class="btn btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
