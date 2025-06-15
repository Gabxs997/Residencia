<?php
require_once __DIR__ . '/../../config/conexion.php';
// Obtener proveedores y áreas únicos
$proveedores = [];
$areas = [];
$res1 = mysqli_query($conectar, "SELECT DISTINCT p.id, p.razon_social FROM proveedores p INNER JOIN articulos a ON a.proveedor_id=p.id ORDER BY p.razon_social");
while ($row = mysqli_fetch_assoc($res1)) $proveedores[] = $row;
$res2 = mysqli_query($conectar, "SELECT DISTINCT u.id, u.nombre_area FROM ubicaciones u INNER JOIN articulos a ON a.ubicacion=u.id ORDER BY u.nombre_area");
while ($row = mysqli_fetch_assoc($res2)) $areas[] = $row;

// ¿Hay filtro activo?
$filtro_tipo = isset($_GET['filtro_tipo']) ? $_GET['filtro_tipo'] : '';
$filtro_valor = isset($_GET['filtro_valor']) ? $_GET['filtro_valor'] : '';

$query = "SELECT a.*, 
                 p.razon_social AS proveedor, 
                 u.nombre_area AS area
          FROM articulos a
          LEFT JOIN proveedores p ON a.proveedor_id = p.id
          LEFT JOIN ubicaciones u ON a.ubicacion = u.id";
$where = [];
if ($filtro_tipo == 'proveedor' && $filtro_valor) $where[] = "a.proveedor_id = " . intval($filtro_valor);
if ($filtro_tipo == 'area' && $filtro_valor) $where[] = "a.ubicacion = " . intval($filtro_valor);
if (count($where) > 0) $query .= " WHERE " . implode(' AND ', $where);
$query .= " ORDER BY a.id ASC";
$result = mysqli_query($conectar, $query);

$articulos = [];
while ($row = mysqli_fetch_assoc($result)) $articulos[] = $row;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Tipo Listado</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/reportes.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
    <!-- jsPDF, jsPDF-AutoTable, SheetJS -->
    <script src="../../lib/jspdf.umd.min.js"></script>
    <script src="../../lib/jspdf.plugin.autotable.min.js"></script>
    <script src="../../lib/xlsx.full.min.js"></script>
</head>
<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="user-info"><i class="fas fa-user"></i><span>Técnico</span></div>
    </header>
    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <ul class="sidebar-menu">
            <li><a href="../tecnico.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogoT.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../reportesT.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <h1>
            <a href="../reportesT.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            Reporte Tipo Listado
        </h1>
        <div class="descarga-global-buttons" style="margin-bottom:24px; display: flex; gap: 14px; align-items: center;">
            <button class="btn-descarga" onclick="descargarPDFListado()">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </button>
            <button class="btn-descarga" onclick="descargarExcelListado()">
                <i class="fas fa-file-excel"></i> Descargar Excel
            </button>
            <!-- BOTÓN FILTRO -->
            <button class="btn-filtro" onclick="document.getElementById('modalFiltro').style.display='flex'">
                <i class="fas fa-filter"></i> Filtro
            </button>
            <?php if($filtro_tipo && $filtro_valor): ?>
                <a href="listado.php" class="btn-filtro-reset" style="margin-left:14px">
                    <i class="fas fa-times-circle"></i> Quitar Filtro
                </a>
            <?php endif; ?>
            <?php if($filtro_tipo && $filtro_valor): ?>
                <span class="filtro-activo">
                    <?php
                        echo "<i class='fas fa-filter'></i> ";
                        if ($filtro_tipo=='proveedor') {
                            foreach($proveedores as $prov) if($prov['id']==$filtro_valor) echo "Proveedor: <b>".$prov['razon_social']."</b>";
                        }
                        if ($filtro_tipo=='area') {
                            foreach($areas as $a) if($a['id']==$filtro_valor) echo "Área: <b>".$a['nombre_area']."</b>";
                        }
                    ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="table-container">
            <table class="scrollable-table" id="listadoTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Tipo Bien</th>
                        <th>Área</th>
                        <th>Proveedor</th>
                        <th>Partida Presup.</th>
                        <th>Partida Contable</th>
                        <th>UR</th>
                        <th>No. Inventario</th>
                        <th>Modelo</th>
                        <th>Serie</th>
                        <th>Marca</th>
                        <th>Estado Bien</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($articulos as $art): ?>
                    <tr>
                        <td><?= $art['id'] ?></td>
                        <td><?= htmlspecialchars($art['descripcion']) ?></td>
                        <td><?= htmlspecialchars($art['tipo_bien']) ?></td>
                        <td><?= htmlspecialchars($art['area']) ?></td>
                        <td><?= htmlspecialchars($art['proveedor']) ?></td>
                        <td><?= htmlspecialchars($art['partida_presupuestal']) ?></td>
                        <td><?= htmlspecialchars($art['partida_contable']) ?></td>
                        <td><?= htmlspecialchars($art['ur']) ?></td>
                        <td><?= htmlspecialchars($art['no_inventario']) ?></td>
                        <td><?= htmlspecialchars($art['modelo']) ?></td>
                        <td><?= htmlspecialchars($art['serie']) ?></td>
                        <td><?= htmlspecialchars($art['marca']) ?></td>
                        <td><?= htmlspecialchars($art['estado_bien']) ?></td>
                        <td>$<?= $art['importe'] ? number_format($art['importe'],2) : "0.00" ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal filtro -->
        <div class="modal-overlay" id="modalFiltro" style="display:none; align-items:center;">
            <div class="modal-container" style="width:420px; min-height:170px; padding:32px 24px;">
                <span class="modal-close" onclick="document.getElementById('modalFiltro').style.display='none'"><i class="fas fa-times"></i></span>
                <h2 class="modal-title" style="margin-bottom: 22px;"><i class="fas fa-filter"></i> Filtrar Artículos</h2>
                <form id="filtroForm" method="GET" action="listado.php">
                    <div style="margin-bottom:18px;">
                        <label><i class="fas fa-industry"></i> Proveedor:</label>
                        <select name="filtro_valor" id="filtroProveedor" style="width:100%;">
                            <option value="">Seleccione un proveedor</option>
                            <?php foreach($proveedores as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= ($filtro_tipo=='proveedor' && $filtro_valor==$p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['razon_social']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="margin-bottom:18px;">
                        <label><i class="fas fa-building"></i> Área:</label>
                        <select name="filtro_valor_area" id="filtroArea" style="width:100%;">
                            <option value="">Seleccione un área</option>
                            <?php foreach($areas as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= ($filtro_tipo=='area' && $filtro_valor==$a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre_area']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" class="btn-descarga" onclick="aplicarFiltro('proveedor')">
                            <i class="fas fa-filter"></i> Filtrar por Proveedor
                        </button>
                        <button type="button" class="btn-descarga" onclick="aplicarFiltro('area')">
                            <i class="fas fa-filter"></i> Filtrar por Área
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        function aplicarFiltro(tipo) {
            // Limpia el otro campo para solo filtrar uno a la vez
            if(tipo === 'proveedor') {
                document.getElementById('filtroArea').value = '';
                let valor = document.getElementById('filtroProveedor').value;
                if (!valor) return alert("Selecciona un proveedor válido.");
                window.location = "listado.php?filtro_tipo=proveedor&filtro_valor=" + valor;
            } else if(tipo === 'area') {
                document.getElementById('filtroProveedor').value = '';
                let valor = document.getElementById('filtroArea').value;
                if (!valor) return alert("Selecciona un área válida.");
                window.location = "listado.php?filtro_tipo=area&filtro_valor=" + valor;
            }
        }

        // PDF igual que antes, solo se descargan los datos de la tabla actual (filtrada)
        function descargarPDFListado() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: "landscape",
                unit: "pt",
                format: "A4"
            });
            const logoUrl = '../../Logos/800px-ISSSTE_logo.png';
            const columnas = [
                "ID", "Descripción", "Tipo Bien", "Área", "Proveedor",
                "Partida Presup.", "Partida Contable", "UR", "No. Inventario",
                "Modelo", "Serie", "Marca", "Estado Bien", "Importe"
            ];
            // Solo las filas visibles en la tabla
            const filas = Array.from(document.querySelectorAll('#listadoTable tbody tr'))
                .filter(tr => tr.style.display !== "none")
                .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));

            const img = new window.Image();
            img.crossOrigin = '';
            img.src = logoUrl;
            img.onload = function () {
                doc.setFontSize(28);
                doc.setTextColor('#5c1434');
                doc.text("Listado de Artículos", 60, 65);
                doc.addImage(img, "PNG", 670, 30, 140, 70);

                doc.autoTable({
                    startY: 110,
                    head: [columnas],
                    body: filas,
                    styles: {
                        fontSize: 10,
                        cellPadding: 4,
                        valign: 'middle'
                    },
                    headStyles: {
                        fillColor: [92, 20, 52],
                        textColor: 255,
                        fontStyle: 'bold'
                    },
                    bodyStyles: {
                        fillColor: [245, 241, 233],
                        textColor: [44, 44, 44]
                    },
                    alternateRowStyles: {
                        fillColor: [255,255,255]
                    },
                    margin: { left: 55, right: 30 }
                });

                doc.save(`listado_articulos.pdf`);
            };
            img.onerror = function () {
                generarPDFListadoSinLogo(doc, columnas, filas);
            };
        }

        // Excel igual (solo lo visible)
        function descargarExcelListado() {
            const columnas = [
                "ID", "Descripción", "Tipo Bien", "Área", "Proveedor",
                "Partida Presup.", "Partida Contable", "UR", "No. Inventario",
                "Modelo", "Serie", "Marca", "Estado Bien", "Importe"
            ];
            const filas = Array.from(document.querySelectorAll('#listadoTable tbody tr'))
                .filter(tr => tr.style.display !== "none")
                .map(tr => Array.from(tr.children).map(td => td.textContent.trim()));
            const data = [columnas, ...filas];
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Listado Artículos");
            XLSX.writeFile(wb, `listado_articulos.xlsx`);
        }

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }
    </script>
</body>
</html>


