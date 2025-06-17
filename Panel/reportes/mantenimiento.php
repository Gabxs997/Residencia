<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Procesar registro de mantenimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_mantenimiento'])) {
    $idArticulo = $_POST['articulo_id'];
    $fecha = $_POST['fecha_mantenimiento'];
    $tipo = $_POST['tipo'];
    $estatus = $_POST['estatus'];
    $respuesta = in_array($estatus, ['En proceso', 'Terminado']) ? $_POST['respuesta'] : '';

    $observaciones = $_POST['descripcion_problema'] ?? '';

    $prox = new DateTime($fecha);
    $prox->modify('+6 months');
    $siguiente = $prox->format('Y-m-d');

    // Insertar mantenimiento
    mysqli_query($conectar, "
    INSERT INTO mantenimientos (articulo_id, fecha_mantenimiento, siguiente_mantenimiento, tipo, estatus, respuesta, observaciones)
    VALUES ('$idArticulo', '$fecha', '$siguiente', '$tipo', '$estatus', '$respuesta', '$observaciones')
");


    // Obtener ID recién insertado
    $mantenimiento_id = mysqli_insert_id($conectar);

    // Actualizar siguiente mantenimiento en artículos
    mysqli_query($conectar, "
    UPDATE articulos SET siguiente_mantenimiento = '$siguiente' WHERE id = $idArticulo
");

    // Convertir estatus a número
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
        $solicitud_id = $row['id'];

        // Vincular solicitud con el mantenimiento (respuesta_id)
        mysqli_query($conectar, "
        UPDATE solicitudes_mantenimiento 
        SET estatus = $estatus_int, respuesta_id = $mantenimiento_id 
        WHERE id = $solicitud_id
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
    <link rel="stylesheet" href="../../font/css/all.min.css">
    <script src="../../lib/jspdf.umd.min.js"></script>
    <script src="../../lib/jspdf.plugin.autotable.min.js"></script>
    <script src="../../lib/xlsx.full.min.js"></script>

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
                        <th>Fecha de Solicitud</th>
                        <th>Solicitante</th>
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
                            <td><?= date("Y-m-d h:i A", strtotime($sol['fecha_solicitud'])) ?></td>
                            <td><?= htmlspecialchars($sol['nombre_solicitante']) ?></td>
                            <td>
                                <?= match ((string)$sol['estatus_mantenimiento']) {
                                    '1' => "<span class='badge-sm badge-proceso'>En proceso</span>",
                                    '2' => "<span class='badge-sm badge-terminado'>Terminado</span>",
                                    '3' => "<span class='badge-sm badge-cancelado'>Cancelado</span>",
                                    default => "<span class='badge-sm badge-pendiente'>Pendiente</span>"
                                } ?>
                            </td>
                            <td class="accion-centrada">
                                <button class="btn-detalles" onclick='mostrarSolicitud(<?= json_encode($sol) ?>)'>
                                    <i class="fas fa-eye"></i> Ver
                                </button>

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
        <!-- Modal Detalles de Solicitud -->
        <div class="modal-overlay" id="modalSolicitud">
            <div class="modal-container-flat">
                <span class="modal-close" onclick="cerrarModalSolicitud()"><i class="fas fa-times"></i></span>
                <h2 class="modal-title"><i class="fas fa-info-circle"></i> Detalles de la Solicitud</h2>

                <div class="modal-field-flat"><strong>ID del Artículo:</strong> <span id="ver_id"></span></div>
                <div class="modal-field-flat"><strong>Descripción:</strong> <span id="ver_descripcion"></span></div>
                <div class="modal-field-flat"><strong>Área:</strong> <span id="ver_area"></span></div>
                <div class="modal-field-flat"><strong>Proveedor:</strong> <span id="ver_proveedor"></span></div>
                <div class="modal-field-flat"><strong>Fecha de Solicitud:</strong> <span id="ver_fecha"></span></div>
                <div class="modal-field-flat"><strong>Solicitante:</strong> <span id="ver_solicitante"></span></div>
                <div class="modal-field-flat"><strong>Descripción del Problema:</strong> <span id="ver_problema" style="white-space: pre-wrap;"></span></div>
                <div class="modal-field-flat"><strong>Estatus:</strong> <span id="ver_estatus"></span></div>

                <div class="modal-buttons" style="margin-top: 25px; display: flex; justify-content: center; gap: 18px;">
                    <button class="btn-cancelar" onclick="cerrarModalSolicitud()">
                        <i class="fas fa-times-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>


                     <!--Sección de historial de mantenimientos-->


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
        $filtro_inicio = $_GET['filtro_inicio'] ?? '';
        $filtro_fin = $_GET['filtro_fin'] ?? '';

        $where = ["s.visible = 1"];
        if ($filtro_area !== '') $where[] = "a.ubicacion = " . intval($filtro_area);
        if ($filtro_estatus !== '') $where[] = "s.estatus = " . intval($filtro_estatus);
        if ($filtro_inicio !== '' && $filtro_fin !== '') {
            $inicio = mysqli_real_escape_string($conectar, $filtro_inicio);
            $fin = mysqli_real_escape_string($conectar, $filtro_fin);
            $where[] = "DATE(s.fecha_solicitud) BETWEEN '$inicio' AND '$fin'";
        }


        $query_historial = "SELECT 
    s.fecha_solicitud, s.nombre_solicitante, s.descripcion_problema,
    a.descripcion AS articulo, u.nombre_area, s.estatus,
    m.tipo
FROM solicitudes_mantenimiento s
INNER JOIN articulos a ON s.articulo_id = a.id
LEFT JOIN ubicaciones u ON a.ubicacion = u.id
LEFT JOIN mantenimientos m ON m.id = s.respuesta_id
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
        if ($filtro_inicio !== '' && $filtro_fin !== '') {
            $filtroNombre .= ($filtroNombre ? ' | ' : '') . "Rango: $filtro_inicio a $filtro_fin";
        }
        // Construir URL para quitar filtros
$quitarFiltroURL = basename($_SERVER['PHP_SELF']);

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
            <?php if ($filtro_area || $filtro_estatus || $filtro_inicio || $filtro_fin): ?>
                <a href="<?= $quitarFiltroURL ?>" class="btn-filtro-reset">
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
                        <th>Tipo</th>
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

                            <td><?= date("Y-m-d h:i A", strtotime($row['fecha_solicitud'])) ?></td>

                            <td><?= htmlspecialchars($row['tipo'] ?? '—') ?></td>

                            <td class="descripcion-wrap"><?= nl2br(htmlspecialchars($row['descripcion_problema'])) ?></td>


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
                    <div class="modal-field filtro-modal">
                        <label><i class="fas fa-calendar-alt"></i> Fecha Inicio:</label>
                        <input type="date" name="filtro_inicio" value="<?= htmlspecialchars($_GET['filtro_inicio'] ?? '') ?>">
                    </div>
                    <div class="modal-field filtro-modal">
                        <label><i class="fas fa-calendar-check"></i> Fecha Fin:</label>
                        <input type="date" name="filtro_fin" value="<?= htmlspecialchars($_GET['filtro_fin'] ?? '') ?>">
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
            campo.style.display = (valor === 'En proceso') ? 'block' : 'none';
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
            const doc = new jsPDF({
                orientation: "landscape"
            });

            const columnas = ["Área", "Artículo", "Solicitante", "Fecha y Hora", "Tipo", "Descripción", "Estatus"];
            const filas = Array.from(document.querySelectorAll('#tablaHistorial tbody tr'))
                .filter(tr => tr.style.display !== 'none')
                .map(tr => {
                    const tds = tr.children;
                    const fechaCompleta = tds[3].textContent.trim();
                    const [fecha, hora] = fechaCompleta.split(' ');
                    return [
                        tds[0].textContent.trim(),
                        tds[1].textContent.trim(),
                        tds[2].textContent.trim(),
                        `${fecha}\n${hora || ''}`,
                        tds[4].textContent.trim(),
                        tds[5].textContent.trim(),
                        tds[6].textContent.trim()
                    ];
                });

            const colWidths = [25, 35, 35, 30, 20, 70, 25];
            const totalWidth = colWidths.reduce((sum, w) => sum + w, 0);
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const marginLeft = (pageWidth - totalWidth) / 2;

            const header = new Image();
            header.src = '../../Logos/headerVale.png';
            const footer = new Image();
            footer.src = '../../Logos/footerVale.png';

            header.onload = () => {
                footer.onload = () => {
                    const imgWidth = 180;
                    const imgX = (pageWidth - imgWidth) / 2;
                    const headerY = 10;
                    const footerHeight = 18;
                    const footerY = pageHeight - footerHeight - 5;

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(14);
                    doc.text("Historial de Solicitudes de Mantenimiento", pageWidth / 2, 40, {
                        align: 'center'
                    });

                    if ("<?= $filtroNombre ?>") {
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(12);
                        doc.text("<?= $filtroNombre ?>", pageWidth / 2, 33, {
                            align: 'center'
                        });
                    }

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(12);
                    doc.text(`Total: <?= $totalFiltrados ?> solicitud(es)`, pageWidth / 2, 46, {
                        align: 'center'
                    });

                    doc.autoTable({
                        head: [columnas],
                        body: filas,
                        startY: 52,
                        margin: {
                            top: 35,
                            left: marginLeft,
                            bottom: footerHeight + 15
                        },
                        styles: {
                            fontSize: 8,
                            cellPadding: 2,
                            valign: 'top'
                        },
                        columnStyles: {
                            0: {
                                cellWidth: colWidths[0]
                            },
                            1: {
                                cellWidth: colWidths[1]
                            },
                            2: {
                                cellWidth: colWidths[2]
                            },
                            3: {
                                cellWidth: colWidths[3]
                            },
                            4: {
                                cellWidth: colWidths[4]
                            },
                            5: {
                                cellWidth: colWidths[5]
                            },
                            6: {
                                cellWidth: colWidths[6]
                            }
                        },
                        headStyles: {
                            fillColor: [41, 128, 185],
                            textColor: [255, 255, 255],
                            halign: 'center'
                        },
                        bodyStyles: {
                            halign: 'left'
                        },
                        theme: 'striped',
                        didDrawPage: function(data) {
                            doc.addImage(header, 'PNG', imgX, headerY, imgWidth, 15);
                            doc.addImage(footer, 'PNG', imgX, footerY, imgWidth, footerHeight);

                            const pageSize = doc.internal.pageSize;
                            const pageWidth = pageSize.width;
                            const pageHeight = pageSize.height;

                            const str = "Página " + doc.internal.getCurrentPageInfo().pageNumber + " de {totalPages}";
                            doc.setFontSize(8);
                            doc.setTextColor(100);
                            doc.text(str, pageWidth - 50, pageHeight - 5);
                        }
                    });
                    doc.putTotalPages("{totalPages}");


                    doc.save("historial_solicitudes.pdf");
                };
            };
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
    <script>
        function mostrarSolicitud(data) {
            document.getElementById('ver_id').textContent = data.articulo_id;
            document.getElementById('ver_descripcion').textContent = data.descripcion || '';
            document.getElementById('ver_area').textContent = data.nombre_area || '';
            document.getElementById('ver_proveedor').textContent = data.razon_social || '';
            document.getElementById('ver_fecha').textContent = data.fecha_solicitud || '';
            document.getElementById('ver_solicitante').textContent = data.nombre_solicitante || '';
            document.getElementById('ver_problema').textContent = data.descripcion_problema || '';
            document.getElementById('ver_estatus').innerHTML = {
                '1': "<span class='badge-sm badge-proceso'>En proceso</span>",
                '2': "<span class='badge-sm badge-terminado'>Terminado</span>",
                '3': "<span class='badge-sm badge-cancelado'>Cancelado</span>"
            } [data.estatus_mantenimiento] || "<span class='badge-sm badge-pendiente'>Pendiente</span>";

            document.getElementById('modalSolicitud').style.display = 'flex';
        }

        function cerrarModalSolicitud() {
            document.getElementById('modalSolicitud').style.display = 'none';
        }
    </script>


</body>

</html>