<?php
require_once __DIR__ . '/../../config/conexion.php';
$query = "SELECT a.*, 
                 p.razon_social AS proveedor, 
                 u.nombre_area AS area,
                 u.rfc AS rfc_responsable
          FROM articulos a
          LEFT JOIN proveedores p ON a.proveedor_id = p.id
          LEFT JOIN ubicaciones u ON a.ubicacion = u.id
          ORDER BY a.id ASC";
$result = mysqli_query($conectar, $query);

$articulos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articulos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Tipo Bien</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/reportes.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
    <!-- jsPDF y SheetJS para generación de PDF/Excel -->
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
        <h1>
            <a href="../reportes.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            Reporte Tipo Bien
        </h1><br><br><br>
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar artículos..." class="search-input">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="bienTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Tipo Bien</th>
                        <th>Área</th>
                        <th>Proveedor</th>
                        <th>Partida Presup.</th>
                        <th>Partida Contable</th>
                        <th>Acciones</th>
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
                            <td>
                                <button class="btn-reporte" onclick='mostrarModal(<?= json_encode($art, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="fas fa-file-alt"></i> Generar reporte
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal -->
        <div class="modal-overlay" id="modalOverlay">
            <div class="modal-container">
                <span class="modal-close" onclick="cerrarModal()"><i class="fas fa-times"></i></span>
                <h2 class="modal-title"><i class="fas fa-info-circle"></i> Reporte de Artículo</h2>
                <div class="modal-details" id="modalDetails"></div>
                <div class="modal-buttons">
                    <button class="btn-descarga" onclick="descargarPDF()">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </button>
                    <button class="btn-descarga" onclick="descargarExcel()">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                    </button>
                </div>
            </div>
        </div>
    </main>
    <script>
        let datosArticulo = {};

        function mostrarModal(articulo) {
            datosArticulo = articulo;
            const detalles = document.getElementById('modalDetails');
            // ORGANIZA EN 3 COLUMNAS, CADA CAMPO EN SU CAJA
            const campos = [
                ["ID", articulo.id],
                ["UR", articulo.ur],
                ["No. Inventario", articulo.no_inventario],
                ["CABM", articulo.cabm],
                ["Descripción", articulo.descripcion],
                ["Detalle", articulo.descripcion_detalle],
                ["Partida Presupuestal", articulo.partida_presupuestal],
                ["Partida Contable", articulo.partida_contable],
                ["Fecha Alta", articulo.fecha_alta],
                ["Fecha Documento", articulo.fecha_documento],
                ["Tipo Bien", articulo.tipo_bien],
                ["No. Contrato", articulo.no_contrato],
                ["No. Factura", articulo.no_factura],
                ["Proveedor", articulo.proveedor],
                ["Serie", articulo.serie],
                ["Modelo", articulo.modelo],
                ["Marca", articulo.marca],
                ["Estado Bien", articulo.estado_bien],
                ["Área", articulo.area],
                ["RFC Responsable", articulo.rfc_responsable],
                ["Importe", "$" + (articulo.importe ? parseFloat(articulo.importe).toFixed(2) : "0.00")],
                ["Observaciones", articulo.observaciones],
                ["Origen", articulo.origen],
                ["Fecha Registro", articulo.fecha_registro ? articulo.fecha_registro.substring(0, 19).replace('T', ' ') : '']
            ];
            let html = `<div class="modal-details-grid">`;
            campos.forEach(([label, value]) => {
                html += `
                <div class="modal-field">
                    <span class="modal-label">${label}:</span>
                    <span class="modal-value">${value ? value : '<span class="modal-vacio">N/A</span>'}</span>
                </div>`;
            });
            html += `</div>`;
            detalles.innerHTML = html;
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalOverlay').style.display = 'none';
        }

        // PDF bonito con columnas y recuadros 
        function descargarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "A4" });

    const header = new Image();
    const footer = new Image();
    header.src = '../../Logos/headerVale.png';
    footer.src = '../../Logos/footerVale.png';

    header.onload = () => {
        footer.onload = () => {
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const imgWidth = 520;
            const imgHeight = 55;
            const imgX = (pageWidth - imgWidth) / 2;
            const headerY = 15;
            const footerHeight = 45;
            const footerY = pageHeight - footerHeight - 5;

            const campos = [
                ["ID", datosArticulo.id],
                ["UR", datosArticulo.ur],
                ["No. Inventario", datosArticulo.no_inventario],
                ["CABM", datosArticulo.cabm],
                ["Descripción", datosArticulo.descripcion],
                ["Detalle", datosArticulo.descripcion_detalle],
                ["Partida Presup.", datosArticulo.partida_presupuestal],
                ["Partida Contable", datosArticulo.partida_contable],
                ["Fecha Alta", datosArticulo.fecha_alta],
                ["Fecha Documento", datosArticulo.fecha_documento],
                ["Tipo Bien", datosArticulo.tipo_bien],
                ["No. Contrato", datosArticulo.no_contrato],
                ["No. Factura", datosArticulo.no_factura],
                ["Proveedor", datosArticulo.proveedor],
                ["Serie", datosArticulo.serie],
                ["Modelo", datosArticulo.modelo],
                ["Marca", datosArticulo.marca],
                ["Estado Bien", datosArticulo.estado_bien],
                ["Área", datosArticulo.area],
                ["RFC Resp.", datosArticulo.rfc_responsable],
                ["Importe", "$" + (datosArticulo.importe ? parseFloat(datosArticulo.importe).toFixed(2) : "0.00")],
                ["Observaciones", datosArticulo.observaciones],
                ["Origen", datosArticulo.origen],
                ["Fecha Registro", datosArticulo.fecha_registro]
            ];

            const colCount = 2;
            const rowH = 50;
            const boxW = (pageWidth - 100) / colCount;
            const startX = 50;
            const startY = 120;
            const padding = 10;
            let x = startX;
            let y = startY;

            doc.setFontSize(8);

            const totalPagesExp = "{totalPages}";
            let currentPage = 1;
            const pageNumbers = [];

            function drawPageHeaderFooter() {
                doc.addImage(header, 'PNG', imgX, headerY, imgWidth, imgHeight);
                doc.setFontSize(14);
                doc.setTextColor('#5c1434');
                doc.text("REPORTE DE BIEN INMUEBLE", pageWidth / 2, 90, { align: 'center' });
                doc.addImage(footer, 'PNG', imgX, footerY, imgWidth, footerHeight);
                doc.setFontSize(8);
                doc.setTextColor(100);
                pageNumbers.push(doc.internal.getNumberOfPages());
            }

            drawPageHeaderFooter();

            campos.forEach(([label, valor], i) => {
                if (i % colCount === 0 && i !== 0) {
                    y += rowH;
                    x = startX;
                }

                if (y + rowH + footerHeight + 20 > pageHeight) {
                    currentPage++;
                    doc.addPage();
                    y = startY;
                    x = startX;
                    drawPageHeaderFooter();
                }

                doc.setFillColor(255, 255, 255);
                doc.roundedRect(x, y, boxW - 25, rowH - 18, 6, 6, 'F');
                doc.setDrawColor(200, 200, 200);
                doc.roundedRect(x, y, boxW - 25, rowH - 18, 6, 6, 'S');

                doc.setFont(undefined, 'bold');
                doc.setTextColor('#5c1434');
                doc.setFontSize(12); // Tamaño del título del campo
                doc.text(label + ":", x + padding, y + 14);

                doc.setFont(undefined, 'normal');
                doc.setFontSize(10); // Tamaño de texto para la información
                doc.setTextColor('#111');
                doc.text((valor ? String(valor) : 'N/A'), x + padding, y + 28, {
                    maxWidth: boxW - 45
                });

                x += boxW;
            });

            // Número de páginas final
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(100);
                doc.text(`Página ${i} de ${pageCount}`, pageWidth - 80, pageHeight - 10);
            }

            doc.save(`articulo_${datosArticulo.id}.pdf`);
        };
    };

    header.onerror = function () {
        alert('No se pudo cargar el header/footer, se generará el PDF sin ellos.');
    };
}


        // Excel con SheetJS
        function descargarExcel() {
            const data = [
                Object.keys(datosArticulo).map(campo => formatoCampo(campo)),
                Object.values(datosArticulo)
            ];
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Reporte Artículo");
            XLSX.writeFile(wb, `articulo_${datosArticulo.id}.xlsx`);
        }

        function formatoCampo(key) {
            const nombres = {
                id: "ID",
                ur: "UR",
                no_inventario: "No. Inventario",
                cabm: "CABM",
                descripcion: "Descripción",
                descripcion_detalle: "Desc. Detalle",
                partida_presupuestal: "Partida Presupuestal",
                partida_contable: "Partida Contable",
                fecha_alta: "Fecha Alta",
                fecha_documento: "Fecha Documento",
                tipo_bien: "Tipo Bien",
                no_contrato: "No. Contrato",
                no_factura: "No. Factura",
                proveedor: "Proveedor",
                serie: "Serie",
                modelo: "Modelo",
                marca: "Marca",
                estado_bien: "Estado Bien",
                area: "Área",
                rfc_responsable: "RFC Responsable",
                importe: "Importe",
                observaciones: "Observaciones",
                origen: "Origen",
                fecha_registro: "Fecha Registro"
            };
            return nombres[key] ?? key;
        }

        // Transición para el botón reporte
        document.querySelectorAll('.btn-reporte').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });
            btn.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }
    </script>

    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#bienTable tbody tr');
            let maxMatches = 0;
            const rowMatches = [];

            rows.forEach(row => {
                let matchCount = 0;
                const cells = row.querySelectorAll('td');

                // Guardar el estado original del badge antes de cualquier modificación
                const estadoBadge = row.querySelector('.estado');
                const originalEstadoHTML = estadoBadge ? estadoBadge.outerHTML : '';

                // Quitar resaltados anteriores (excepto en el badge de estado)
                row.querySelectorAll('.highlight').forEach(el => {
                    if (!el.closest('.estado')) { // No remover resaltado del badge de estado
                        el.outerHTML = el.innerHTML;
                    }
                });

                // Restaurar el badge de estado a su versión original
                if (estadoBadge) {
                    estadoBadge.outerHTML = originalEstadoHTML;
                }

                // Buscar coincidencias
                cells.forEach((cell, index) => {
                    if (index !== cells.length - 1) { // Excluir columna de acciones
                        const text = cell.textContent.toLowerCase();
                        if (searchTerm !== '') {
                            const regex = new RegExp(searchTerm, 'gi');
                            const matches = text.match(regex);
                            if (matches) {
                                matchCount += matches.length;
                                // Resaltar coincidencias (excepto en el badge de estado)
                                if (!cell.querySelector('.estado')) {
                                    cell.innerHTML = cell.textContent.replace(
                                        regex,
                                        match => `<span class="highlight">${match}</span>`
                                    );
                                }
                            }
                        }
                    }
                });

                rowMatches.push({
                    row,
                    matchCount
                });
                if (matchCount > maxMatches) {
                    maxMatches = matchCount;
                }
            });

            // Mostrar resultados
            rowMatches.forEach(item => {
                item.row.style.display = (searchTerm === '' || item.matchCount > 0) ? '' : 'none';
            });

        });
    </script>
</body>

</html>