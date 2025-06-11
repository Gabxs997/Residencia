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

    $prox = new DateTime($fecha);
    $prox->modify('+6 months');
    $siguiente = $prox->format('Y-m-d');

    mysqli_query($conectar, "
        INSERT INTO mantenimientos (articulo_id, fecha_mantenimiento, siguiente_mantenimiento, tipo, estatus, respuesta, observaciones)
        VALUES ('$idArticulo', '$fecha', '$siguiente', '$tipo', '$estatus', '$respuesta', '$observaciones')
    ");

    mysqli_query($conectar, "
        UPDATE articulos SET siguiente_mantenimiento = '$siguiente' WHERE id = $idArticulo
    ");

    $estatus_int = match ($estatus) {
        'Pendiente' => 0,
        'En proceso' => 1,
        'Terminado' => 2,
        default => 0
    };

    $res = mysqli_query($conectar, "
        SELECT id FROM solicitudes_mantenimiento 
        WHERE articulo_id = $idArticulo 
        ORDER BY id DESC LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {
        $ultima_solicitud_id = $row['id'];

        mysqli_query($conectar, "
            UPDATE solicitudes_mantenimiento 
            SET estatus = $estatus_int 
            WHERE id = $ultima_solicitud_id
        ");
    }

    header("Location: mantenimiento.php?refreshed=1");
    exit;
}

// Obtener todas las solicitudes (última por artículo)
$solicitudes = mysqli_query($conectar, "
    SELECT s.id AS solicitud_id, s.fecha_solicitud, s.nombre_solicitante, s.descripcion_problema,
           a.id AS articulo_id, a.descripcion, u.nombre_area, p.razon_social,
           s.estatus AS estatus_mantenimiento
    FROM (
        SELECT * FROM solicitudes_mantenimiento 
        WHERE visible = 1 AND id IN (
            SELECT MAX(id) FROM solicitudes_mantenimiento GROUP BY articulo_id
        )
    ) s
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
    <link rel="stylesheet" href="../../CSS/reportes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
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
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="buscador" placeholder="Buscar en la tabla..." class="buscador-mto">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="tablaMantenimiento">
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
                                <?= match ((string)$sol['estatus_mantenimiento']) {
                                    '1' => "<span class='badge-sm badge-proceso'>En proceso</span>",
                                    '2' => "<span class='badge-sm badge-terminado'>Terminado</span>",
                                    '3' => "<span class='badge-sm badge-cancelado'>Cancelado</span>",
                                    default => "<span class='badge-sm badge-pendiente'>Pendiente</span>"
                                } ?>

                            </td>
                            <td class="accion-centrada">
                                <?php if (in_array($sol['estatus_mantenimiento'], [2, 3])): ?>
                                    <form class="form-mto" method="POST" action="../actions/eliminarSolicitud.php" onsubmit="return confirm('¿Eliminar esta solicitud?');">
                                        <input type="hidden" name="solicitud_id" value="<?= $sol['solicitud_id'] ?>">
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


        <?php
        // Obtener historial de solicitudes filtradas
        $areas = [];
        $res = mysqli_query($conectar, "SELECT DISTINCT u.id, u.nombre_area FROM ubicaciones u INNER JOIN articulos a ON a.ubicacion=u.id ORDER BY u.nombre_area");
        while ($row = mysqli_fetch_assoc($res)) $areas[] = $row;

        $estatus_opciones = [
            0 => 'Pendiente',
            1 => 'En proceso',
            2 => 'Terminado',
            3 => 'Cancelado'
        ];

        $filtro_area = $_GET['filtro_area'] ?? '';
        $filtro_estatus = $_GET['filtro_estatus'] ?? '';
        $filtro_mes = $_GET['filtro_mes'] ?? '';

        $where = ["s.visible = 1"];
        if ($filtro_area !== '') $where[] = "a.ubicacion = " . intval($filtro_area);
        if ($filtro_estatus !== '') $where[] = "s.estatus = " . intval($filtro_estatus);
        if ($filtro_mes !== '') $where[] = "MONTH(s.fecha_solicitud) = " . intval($filtro_mes);

        $query_historial = "SELECT s.fecha_solicitud, s.nombre_solicitante, s.descripcion_problema,
           a.descripcion AS articulo, u.nombre_area, s.estatus
    FROM solicitudes_mantenimiento s
    INNER JOIN articulos a ON s.articulo_id = a.id
    LEFT JOIN ubicaciones u ON a.ubicacion = u.id
    WHERE " . implode(" AND ", $where) . "
    ORDER BY u.nombre_area ASC, s.estatus ASC, s.fecha_solicitud DESC";

        $historialSolicitudes = mysqli_query($conectar, $query_historial);
        $totalFiltrados = mysqli_num_rows($historialSolicitudes);
        $filtroNombre = '';
        if ($filtro_area !== '') {
            foreach ($areas as $a) {
                if ($a['id'] == $filtro_area) {
                    $filtroNombre = 'Área: ' . $a['nombre_area'];
                    break;
                }
            }
        }
        if ($filtro_estatus !== '') {
            $filtroNombre .= ($filtroNombre ? ' | ' : '') . 'Estatus: ' . $estatus_opciones[$filtro_estatus];
        }
        if ($filtro_mes !== '') {
            $mesNombre = date("F", mktime(0, 0, 0, intval($filtro_mes), 10));
            $filtroNombre .= ($filtroNombre ? ' | ' : '') . 'Mes: ' . $mesNombre;
        }
        ?>
        <br><br>
        <h2 style="margin-top:50px;">Historial de Solicitudes</h2><br><br>
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="buscadorHistorial" placeholder="Buscar en historial..." class="buscador-mto">
        </div>
        <div style="margin: 15px 0; display: flex; gap: 10px;">
            <button class="btn-descarga" onclick="descargarPDFHistorial()">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </button>
            <button class="btn-descarga" onclick="descargarExcelHistorial()">
                <i class="fas fa-file-excel"></i> Descargar Excel
            </button>
            <button class="btn-descarga" onclick="document.getElementById('modalFiltro').style.display='flex'">
                <i class="fas fa-filter"></i> Filtro
            </button>
            <?php if ($filtro_area || $filtro_estatus || $filtro_mes): ?>
                <a href="mantenimiento.php" class="btn-filtro-reset" style="margin-left:14px">
                    <i class="fas fa-times-circle"></i> Quitar Filtro
                </a>
            <?php endif; ?>
        </div>
        <!-- Cantidad de articulos encontrados-->
        <p><strong>Total encontrados:</strong> <?= $totalFiltrados ?> solicitud(es) de mantenimiento</p>

        <!--Tabla de historial de solicitudes-->
        <div class="table-container" style="margin-top:20px;">

            <table class="scrollable-table" id="tablaHistorial">
                <thead>
                    <tr>
                        <th>Área</th>
                        <th>Artículo</th>
                        <th>Solicitante</th>
                        <th>Fecha de Solicitud</th>
                        <th>Descripción del Problema</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($historialSolicitudes)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre_area']) ?></td>
                            <td><?= htmlspecialchars($row['articulo']) ?></td>
                            <td><?= htmlspecialchars($row['nombre_solicitante']) ?></td>
                            <td><?= date("Y-m-d", strtotime($row['fecha_solicitud'])) ?></td>
                            <td><?= htmlspecialchars($row['descripcion_problema']) ?></td>
                            <td>
                                <?= match ((string)$row['estatus']) {
                                    '1' => "<span class='badge-sm badge-proceso'>En proceso</span>",
                                    '2' => "<span class='badge-sm badge-terminado'>Terminado</span>",
                                    '3' => "<span class='badge-sm badge-cancelado'>Cancelado</span>",
                                    default => "<span class='badge-sm badge-pendiente'>Pendiente</span>"
                                } ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal Filtro -->
        <!-- Modal Filtro corregido -->
        <div class="modal-overlay" id="modalFiltro" style="display:none; align-items:center;">
            <div class="modal-container" style="width:420px; min-height:170px; padding:32px 24px;">
                <span class="modal-close" onclick="document.getElementById('modalFiltro').style.display='none'">
                    <i class="fas fa-times"></i>
                </span>
                <h2 class="modal-title" style="margin-bottom: 22px;"><i class="fas fa-filter"></i> Filtrar Historial</h2>
                <form method="GET" action="mantenimiento.php">
                    <div class="modal-field">
                        <label><i class="fas fa-building"></i> Área:</label>
                        <select name="filtro_area" style="width:100%;">
                            <option value="">Seleccione un área</option>
                            <?php foreach ($areas as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= ($_GET['filtro_area'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre_area']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label><i class="fas fa-clipboard-list"></i> Estatus:</label>
                        <select name="filtro_estatus" style="width:100%;">
                            <option value="">Seleccione un estatus</option>
                            <?php foreach ($estatus_opciones as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($_GET['filtro_estatus'] ?? '') == $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label><i class="fas fa-calendar"></i> Mes:</label>
                        <select name="filtro_mes" style="width:100%;">
                            <option value="">Seleccione un mes</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($_GET['filtro_mes'] ?? '') == $m ? 'selected' : '' ?>>
                                    <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="submit" class="btn-descarga">
                            <i class="fas fa-search"></i> Aplicar Filtro
                        </button>
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

    <script>
        const input = document.getElementById('buscador');
        if (input) {
            input.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tablaMantenimiento tbody tr');
                let maxMatches = 0;
                const matches = [];

                rows.forEach(row => {
                    let count = 0;
                    const cells = row.querySelectorAll('td');

                    row.querySelectorAll('.highlight').forEach(el => el.outerHTML = el.innerHTML);

                    cells.forEach(cell => {
                        const text = cell.textContent.toLowerCase();
                        if (term !== '') {
                            const regex = new RegExp(term, 'gi');
                            const found = text.match(regex);
                            if (found) {
                                count += found.length;
                                cell.innerHTML = cell.textContent.replace(regex, m => `<span class="highlight">${m}</span>`);
                            }
                        }
                    });

                    matches.push({
                        row,
                        count
                    });
                    if (count > maxMatches) maxMatches = count;
                });

                matches.forEach(m => {
                    m.row.style.display = (term === '' || (m.count === maxMatches && m.count > 0)) ? '' : 'none';
                });
            });
        }
    </script>

    <script>
        const inputHistorial = document.getElementById('buscadorHistorial');
        if (inputHistorial) {
            inputHistorial.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tablaHistorial tbody tr');
                let maxMatches = 0;
                const matches = [];

                rows.forEach(row => {
                    let count = 0;
                    const cells = row.querySelectorAll('td');

                    row.querySelectorAll('.highlight').forEach(el => el.outerHTML = el.innerHTML);

                    cells.forEach(cell => {
                        const text = cell.textContent.toLowerCase();
                        if (term !== '') {
                            const regex = new RegExp(term, 'gi');
                            const found = text.match(regex);
                            if (found) {
                                count += found.length;
                                cell.innerHTML = cell.textContent.replace(regex, m => `<span class="highlight">${m}</span>`);
                            }
                        }
                    });

                    matches.push({
                        row,
                        count
                    });
                    if (count > maxMatches) maxMatches = count;
                });

                matches.forEach(m => {
                    m.row.style.display = (term === '' || (m.count === maxMatches && m.count > 0)) ? '' : 'none';
                });
            });
        }
    </script>
    <!--Script para descargar en pdf y excel-->
    <script>
        function descargarPDFHistorial() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            const columnas = ["Área", "Artículo", "Solicitante", "Fecha", "Descripción", "Estatus"];
            const filas = Array.from(document.querySelectorAll('#tablaHistorial tbody tr'))
                .filter(tr => tr.style.display !== 'none')
                .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));

            doc.setFontSize(14);
            doc.text("Historial de Solicitudes de Mantenimiento", 14, 20);
            if ("<?= $filtroNombre ?>") doc.text("<?= $filtroNombre ?>", 14, 28);
            doc.text("Total: <?= $totalFiltrados ?> solicitud(es)", 14, 36);

            doc.autoTable({
                head: [columnas],
                body: filas,
                startY: 42
            });
            doc.save("historial_solicitudes.pdf");
        }

        function descargarExcelHistorial() {
            const columnas = ["Área", "Artículo", "Solicitante", "Fecha", "Descripción", "Estatus"];
            const filas = Array.from(document.querySelectorAll('#tablaHistorial tbody tr'))
                .filter(tr => tr.style.display !== 'none')
                .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));
            const data = [
                ["Historial de Solicitudes de Mantenimiento"],
                ["<?= $filtroNombre ?>"],
                ["Total: <?= $totalFiltrados ?> solicitud(es)"],
                [],
                columnas, ...filas
            ];

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Historial");
            XLSX.writeFile(wb, "historial_solicitudes.xlsx");
        }
    </script>


    <script>
        document.querySelectorAll('#modalFiltroHistorial select').forEach(select => {
            select.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const tipo = this.name;
                const valor = selected.getAttribute('data-id');
                this.form.filtro_tipo.value = tipo;
                this.form.filtro_valor.value = valor;
            });
        });
    </script>

</body>

</html>