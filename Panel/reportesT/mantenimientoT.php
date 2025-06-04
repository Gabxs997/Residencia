<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Historial visible/oculto
if (isset($_POST['ocultar_historial'])) $_SESSION['historial_oculto'] = true;
if (isset($_POST['mostrar_historial'])) unset($_SESSION['historial_oculto']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Mantenimientos</title>
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
        <h1><a href="../reportesT.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Módulo de Mantenimiento</h1>

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
                    $conectar = mysqli_connect("localhost", "root", "", "residencia");
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
    </main>

    <script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('active');
        document.querySelector('.main-content').classList.toggle('active');
    }

    document.getElementById('searchHistorialInput').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#historialTable tbody tr');
        let maxMatches = 0;
        const rowMatches = [];

        rows.forEach(row => {
            let matchCount = 0;
            const cells = row.querySelectorAll('td');
            row.querySelectorAll('.highlight').forEach(el => el.outerHTML = el.innerHTML);

            cells.forEach(cell => {
                const text = cell.textContent.toLowerCase();
                if (searchTerm !== '') {
                    const regex = new RegExp(searchTerm, 'gi');
                    const matches = text.match(regex);
                    if (matches) {
                        matchCount += matches.length;
                        cell.innerHTML = cell.textContent.replace(regex, match => `<span class="highlight">${match}</span>`);
                    }
                }
            });

            rowMatches.push({ row, matchCount });
            if (matchCount > maxMatches) maxMatches = matchCount;
        });

        rowMatches.forEach(item => {
            item.row.style.display = (searchTerm === '' || (item.matchCount === maxMatches && item.matchCount > 0)) ? '' : 'none';
        });
    });

    function descargarPDFHistorial() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "A4" });

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
            styles: { fontSize: 10, cellPadding: 4 },
            headStyles: { fillColor: [92, 20, 52], textColor: 255, fontStyle: 'bold' },
            bodyStyles: { fillColor: [245, 241, 233] },
            alternateRowStyles: { fillColor: [255, 255, 255] },
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
