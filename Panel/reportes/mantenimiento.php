<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Procesar registro de mantenimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_mantenimiento'])) {
    $idArticulo = $_POST['articulo_id'];
    $fecha = $_POST['fecha_mantenimiento'];
    $tipo = $_POST['tipo'];
    $estatus = $_POST['estatus'];
    $respuesta = ($estatus === 'Terminado') ? $_POST['respuesta'] : '';
    $observaciones = $_POST['descripcion_problema'] ?? '';

    // Calcular siguiente mantenimiento
    $prox = new DateTime($fecha);
    $prox->modify('+6 months');
    $siguiente = $prox->format('Y-m-d');

    // Insertar mantenimiento
    mysqli_query($conectar, "
        INSERT INTO mantenimientos (articulo_id, fecha_mantenimiento, siguiente_mantenimiento, tipo, estatus, respuesta, observaciones)
        VALUES ('$idArticulo', '$fecha', '$siguiente', '$tipo', '$estatus', '$respuesta', '$observaciones')
    ");

    // Actualizar siguiente mantenimiento del artículo
    mysqli_query($conectar, "
        UPDATE articulos SET siguiente_mantenimiento = '$siguiente' WHERE id = $idArticulo
    ");

    // Mapear estatus textual a número
    $estatus_int = match ($estatus) {
        'Pendiente' => 0,
        'En proceso' => 1,
        'Terminado' => 2,
        default => 0
    };

    // Obtener la última solicitud del artículo
    $res = mysqli_query($conectar, "
        SELECT id FROM solicitudes_mantenimiento 
        WHERE articulo_id = $idArticulo 
        ORDER BY id DESC LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {
        $ultima_solicitud_id = $row['id'];

        // Actualizar solo esa solicitud
        mysqli_query($conectar, "
            UPDATE solicitudes_mantenimiento 
            SET estatus = $estatus_int 
            WHERE id = $ultima_solicitud_id
        ");
    }

    header("Location: mantenimiento.php?refreshed=1");
    echo "Nuevo estatus asignado: $estatus_int";
    exit;
}



// Obtener todas las solicitudes
$solicitudes = mysqli_query($conectar, "
    SELECT s.id, s.estatus, s.fecha_solicitud, s.nombre_solicitante, s.descripcion_problema,
           a.id AS articulo_id, a.descripcion, u.nombre_area, p.razon_social
    FROM solicitudes_mantenimiento s
    INNER JOIN (
        SELECT articulo_id, MAX(id) AS max_id
        FROM solicitudes_mantenimiento
        GROUP BY articulo_id
    ) ultimas ON s.id = ultimas.max_id
    INNER JOIN articulos a ON a.id = s.articulo_id
    LEFT JOIN ubicaciones u ON a.ubicacion = u.id
    LEFT JOIN proveedores p ON a.proveedor_id = p.id
    ORDER BY s.fecha_solicitud DESC
");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mantenimiento - Solicitudes</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/mantenimiento.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="user-info"><i class="fas fa-user"></i><span>Administrador</span></div>
    </header>
    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <ul class="sidebar-menu">
            <li><a href="../administrador.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1><a href="../reportes.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Solicitudes de Mantenimiento</h1><br><br><br>
        <div class="table-container">
            <table class="scrollable-table">
                <thead>
                    <tr>
                        <th>ID Artículo</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Proveedor</th>
                        <th>Fecha de Solicitud</th>
                        <th>Solicitante</th>
                        <th>Problema</th>
                        <th>Estatus</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sol = mysqli_fetch_assoc($solicitudes)): ?>
                        <tr>
                            <td><?= $sol['articulo_id'] ?></td>
                            <td><?= htmlspecialchars($sol['descripcion']) ?></td>
                            <td><?= htmlspecialchars($sol['nombre_area']) ?></td>
                            <td><?= htmlspecialchars($sol['razon_social']) ?></td>
                            <td><?= date("Y-m-d", strtotime($sol['fecha_solicitud'])) ?></td>
                            <td><?= htmlspecialchars($sol['nombre_solicitante']) ?></td>
                            <td><?= htmlspecialchars($sol['descripcion_problema']) ?></td>
                            <td>
                                <?= match ($sol['estatus']) {
                                    1 => "<span class='badge-sm badge-proceso'>En proceso</span>",
                                    2 => "<span class='badge-sm badge-terminado'>Terminado</span>",
                                    default => "<span class='badge-sm badge-pendiente'>Pendiente</span>"
                                } ?>
                            </td>
                            <td class="accion-centrada">
                                <?php if ($sol['estatus'] == 2): ?>
                                    <form class="form-mto" method="POST" action="../actions/eliminarSolicitud.php" onsubmit="return confirm('¿Eliminar esta solicitud?');">
                                        <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                                        <button type="submit" class="btn-eliminar-mto"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-registrar" onclick='abrirModal(<?= $sol['articulo_id'] ?>, <?= json_encode($sol['descripcion_problema']) ?>)'>
                                        <i class="fas fa-tools"></i> Registrar Mantenimiento
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="modal-overlay" id="modalMantenimiento">
            <div class="modal-container">
                <span class="modal-close" onclick="cerrarModal()"><i class="fas fa-times"></i></span>
                <h2 class="modal-title"><i class="fas fa-wrench"></i> Registrar Mantenimiento</h2>
                <form method="POST">
                    <input type="hidden" name="articulo_id" id="articulo_id">
                    <input type="hidden" name="descripcion_problema" id="descripcion_problema">
                    <div class="modal-details-grid">
                        <div class="modal-field">
                            <label>Fecha de Mantenimiento</label>
                            <input class="modal-input-custom" type="date" name="fecha_mantenimiento" required>
                        </div>
                        <div class="modal-field">
                            <label>Tipo</label>
                            <select name="tipo" required>
                                <option value="Preventivo">Preventivo</option>
                                <option value="Correctivo">Correctivo</option>
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Estatus</label>
                            <select name="estatus" id="estatus" required onchange="mostrarRespuesta(this.value)">
                                <option value="Pendiente">Pendiente</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Terminado">Terminado</option>
                            </select>
                        </div>
                        <div class="modal-field" id="campoRespuesta" style="display: none;">
                            <label>Respuesta al Solicitante</label>
                            <textarea name="respuesta" rows="3" class="modal-input-textarea"></textarea>
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button type="submit" class="btn-descarga"><i class="fas fa-save"></i> Guardar</button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModal()"><i class="fas fa-times-circle"></i> Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        }

        function abrirModal(id, problema = '') {
            document.getElementById('articulo_id').value = id;
            document.getElementById('descripcion_problema').value = problema;
            document.getElementById('modalMantenimiento').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalMantenimiento').style.display = 'none';
        }

        function mostrarRespuesta(valor) {
            const campo = document.getElementById('campoRespuesta');
            campo.style.display = (valor === 'Terminado') ? 'block' : 'none';
        }
    </script>
</body>

</html>