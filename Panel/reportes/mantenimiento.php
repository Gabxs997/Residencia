<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Registro de mantenimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_mantenimiento'])) {
    $articuloId = $_POST['articulo_id'];
    $fecha = $_POST['fecha_mantenimiento'];
    $tipo = $_POST['tipo'];
    $obs = $_POST['observaciones'];

    $fechaProx = new DateTime($fecha);
    $fechaProx->modify('+6 months');
    $siguiente = $fechaProx->format('Y-m-d');

    mysqli_query($conectar, "INSERT INTO mantenimientos (articulo_id, fecha_mantenimiento, siguiente_mantenimiento, tipo, observaciones)
                             VALUES ('$articuloId', '$fecha', '$siguiente', '$tipo', '$obs')");

    mysqli_query($conectar, "UPDATE articulos SET siguiente_mantenimiento = '$siguiente' WHERE id = $articuloId");

    header("Location: mantenimiento.php");
    exit;
}

// Historial visible/oculto
if (isset($_POST['ocultar_historial'])) {
    $_SESSION['historial_oculto'] = true;
}
if (isset($_POST['mostrar_historial'])) {
    unset($_SESSION['historial_oculto']);
}

// Obtener artículos
$query = "SELECT a.*, p.razon_social AS proveedor, u.nombre_area AS area FROM articulos a
          LEFT JOIN proveedores p ON a.proveedor_id = p.id
          LEFT JOIN ubicaciones u ON a.ubicacion = u.id
          ORDER BY a.id ASC";
$result = mysqli_query($conectar, $query);
$articulos = [];
while ($row = mysqli_fetch_assoc($result)) $articulos[] = $row;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mantenimiento de Equipos</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/mantenimiento.css">
    <link rel="stylesheet" href="../../CSS/reportes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- jsPDF y AutoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <!-- SheetJS para Excel -->
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
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1><a href="../reportes.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Módulo de Mantenimiento</h1>

        <!-- Buscador -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar mantenimiento..." class="search-input">
        </div>


        <div class="table-container">
            <table class="scrollable-table" id="mantenimientoTable">
                <thead>
                    <tr>
                        <th>ID de Artículo</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Proveedor</th>
                        <th>Fecha Registro</th>
                        <th>Próximo Mto</th>
                        <th>Faltan</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hoy = new DateTime();
                    foreach ($articulos as $art):
                        $prox = new DateTime($art['siguiente_mantenimiento']);
                        $dias = $hoy->diff($prox)->days;
                        $color = $hoy > $prox ? 'rojo' : ($dias <= 30 ? 'rojo' : ($dias <= 60 ? 'naranja' : 'verde'));
                    ?>
                        <tr>
                            <td><?= $art['id'] ?></td>
                            <td><?= htmlspecialchars($art['descripcion']) ?></td>
                            <td><?= htmlspecialchars($art['area']) ?></td>
                            <td><?= htmlspecialchars($art['proveedor']) ?></td>
                            <td><?= date("Y-m-d", strtotime($art['fecha_registro'])) ?></td>
                            <td><?= $art['siguiente_mantenimiento'] ?></td>
                            <td><span class="contador <?= $color ?>"><?= $dias ?> días</span></td>
                            <td>
                                <button class="btn-reporte" onclick="abrirModal(<?= $art['id'] ?>)">
                                    <i class="fas fa-tools"></i> Registrar Mantenimiento
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal-overlay" id="modalMantenimiento">
            <div class="modal-container">
                <span class="modal-close" onclick="cerrarModal()"><i class="fas fa-times"></i></span>
                <h2 class="modal-title"><i class="fas fa-wrench"></i> Registrar Mantenimiento</h2>
                <form method="POST">
                    <input type="hidden" name="articulo_id" id="articulo_id">
                    <div class="modal-details-grid">
                        <div class="modal-field">
                            <label class="modal-label-custom">Fecha de Mantenimiento</label>
                            <input type="date" name="fecha_mantenimiento" class="modal-input-custom" required>
                        </div>
                        <div class="modal-field">
                            <label class="modal-label-custom">Tipo</label>
                            <select name="tipo" class="modal-input-custom" required>
                                <option value="Preventivo">Preventivo</option>
                                <option value="Correctivo">Correctivo</option>
                            </select>
                        </div>
                        <div class="modal-field" style="grid-column: span 3;">
                            <label class="modal-label-custom">Observaciones</label>
                            <textarea name="observaciones" rows="3" class="modal-input-textarea"></textarea>
                        </div>
                    </div>

                    <div class="modal-buttons">
                        <button type="submit" class="btn-descarga">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModal()">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Historial -->
        <?php if (!isset($_SESSION['historial_oculto'])): ?>
            <h2 style="margin-top: 40px;">Historial de Mantenimientos</h2>

            <!-- Buscador para el historial -->
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchHistorialInput" placeholder="Buscar en historial..." class="search-input">
            </div>

            <div class="descarga-global-buttons" style="display:flex; gap: 14px; margin: 20px 0;">
                <button class="btn-descarga" onclick="descargarPDFHistorial()">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
                <button class="btn-descarga" onclick="descargarExcelHistorial()">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
            </div>


            <div class="table-container">
                <table class="scrollable-table" id="historialTable">
                    <thead>
                        <tr>
                            <th>ID Artículo</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                            <th>Próximo</th>
                            <th>Tipo</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $historial = mysqli_query($conectar, "
                SELECT m.articulo_id, a.descripcion, m.fecha_mantenimiento, m.siguiente_mantenimiento, m.tipo, m.observaciones
                FROM mantenimientos m
                INNER JOIN articulos a ON a.id = m.articulo_id
                ORDER BY m.fecha_mantenimiento DESC
            ");
                        while ($h = mysqli_fetch_assoc($historial)):
                        ?>
                            <tr>
                                <td><?= $h['articulo_id'] ?></td>
                                <td><?= htmlspecialchars($h['descripcion']) ?></td>
                                <td><?= $h['fecha_mantenimiento'] ?></td>
                                <td><?= $h['siguiente_mantenimiento'] ?></td>
                                <td><?= $h['tipo'] ?></td>
                                <td><?= htmlspecialchars($h['observaciones']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <form method="POST">
                <div class="historial-toggle">
                    <button type="submit" name="ocultar_historial" class="btn-ocultar">
                        <i class="fas fa-eye-slash"></i> Ocultar historial
                    </button>
                </div>
            </form>
        <?php else: ?>
            <form method="POST" style="margin-top: 40px;">
                <div class="historial-toggle"> <button type="submit" name="mostrar_historial" class="btn-descarga">
                        <i class="fas fa-eye"></i> Mostrar historial
                    </button></div>
            </form>
        <?php endif; ?>

    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        }

        function abrirModal(id) {
            document.getElementById('articulo_id').value = id;
            document.getElementById('modalMantenimiento').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalMantenimiento').style.display = 'none';
        }
    </script>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        }

        function abrirModal(id) {
            document.getElementById('articulo_id').value = id;
            document.getElementById('modalMantenimiento').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalMantenimiento').style.display = 'none';
        }

        // Buscador con resaltado de coincidencias
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#mantenimientoTable tbody tr');
            let maxMatches = 0;
            const rowMatches = [];

            rows.forEach(row => {
                let matchCount = 0;
                const cells = row.querySelectorAll('td');

                // Eliminar resaltados anteriores
                row.querySelectorAll('.highlight').forEach(el => {
                    el.outerHTML = el.innerHTML;
                });

                // Buscar coincidencias
                cells.forEach(cell => {
                    const text = cell.textContent.toLowerCase();
                    if (searchTerm !== '') {
                        const regex = new RegExp(searchTerm, 'gi');
                        const matches = text.match(regex);
                        if (matches) {
                            matchCount += matches.length;
                            cell.innerHTML = cell.textContent.replace(
                                regex,
                                match => `<span class="highlight">${match}</span>`
                            );
                        }
                    }
                });

                rowMatches.push({
                    row,
                    matchCount
                });
                if (matchCount > maxMatches) maxMatches = matchCount;
            });

            // Mostrar solo filas con coincidencias máximas
            rowMatches.forEach(item => {
                item.row.style.display = (searchTerm === '' || (item.matchCount === maxMatches && item.matchCount > 0)) ? '' : 'none';
            });
        });
    </script>

    <script>
        document.getElementById('searchHistorialInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#historialTable tbody tr');
            let maxMatches = 0;
            const rowMatches = [];

            rows.forEach(row => {
                let matchCount = 0;
                const cells = row.querySelectorAll('td');

                // Quitar resaltado anterior
                row.querySelectorAll('.highlight').forEach(el => {
                    el.outerHTML = el.innerHTML;
                });

                // Buscar coincidencias
                cells.forEach(cell => {
                    const text = cell.textContent.toLowerCase();
                    if (searchTerm !== '') {
                        const regex = new RegExp(searchTerm, 'gi');
                        const matches = text.match(regex);
                        if (matches) {
                            matchCount += matches.length;
                            cell.innerHTML = cell.textContent.replace(
                                regex,
                                match => `<span class="highlight">${match}</span>`
                            );
                        }
                    }
                });

                rowMatches.push({
                    row,
                    matchCount
                });
                if (matchCount > maxMatches) maxMatches = matchCount;
            });

            // Mostrar solo filas con coincidencias máximas
            rowMatches.forEach(item => {
                item.row.style.display = (searchTerm === '' || (item.matchCount === maxMatches && item.matchCount > 0)) ? '' : 'none';
            });
        });
    </script>

<script>
function descargarPDFHistorial() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: "landscape",
        unit: "pt",
        format: "A4"
    });

    const columnas = ["ID Artículo", "Descripción", "Fecha", "Próximo", "Tipo", "Observaciones"];
    const filas = Array.from(document.querySelectorAll('#historialTable tbody tr'))
        .filter(tr => tr.style.display !== "none")
        .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));

    doc.setFontSize(18);
    doc.text("Historial de Mantenimientos", 40, 50);
    
    doc.autoTable({
        startY: 70,
        head: [columnas],
        body: filas,
        styles: {
            fontSize: 10,
            cellPadding: 4
        },
        headStyles: {
            fillColor: [92, 20, 52],
            textColor: 255,
            fontStyle: 'bold'
        },
        bodyStyles: {
            fillColor: [245, 241, 233]
        },
        alternateRowStyles: {
            fillColor: [255, 255, 255]
        },
        margin: { left: 40, right: 30 }
    });

    doc.save('historial_mantenimientos.pdf');
}

function descargarExcelHistorial() {
    const columnas = ["ID Artículo", "Descripción", "Fecha", "Próximo", "Tipo", "Observaciones"];
    const filas = Array.from(document.querySelectorAll('#historialTable tbody tr'))
        .filter(tr => tr.style.display !== "none")
        .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));
    const data = [columnas, ...filas];

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Historial");

    XLSX.writeFile(wb, 'historial_mantenimientos.xlsx');
}
</script>




</body>

</html>