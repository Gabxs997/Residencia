<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Validar que el usuario esté autenticado y tenga rol de usuario
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'usuario' || !isset($_SESSION['area_id'])) {
    header('Location: ../login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$area_id = $_SESSION['area_id'];

// Obtener nombre del área
$resArea = mysqli_query($conectar, "SELECT nombre_area FROM ubicaciones WHERE id = $area_id LIMIT 1");
$area = mysqli_fetch_assoc($resArea);
$nombre_area = $area ? $area['nombre_area'] : 'Área desconocida';

// Obtener artículos del área
$query = "SELECT a.*, 
                 p.razon_social AS proveedor, 
                 u.nombre_area AS area
          FROM articulos a
          LEFT JOIN proveedores p ON a.proveedor_id = p.id
          LEFT JOIN ubicaciones u ON a.ubicacion = u.id
          WHERE a.ubicacion = $area_id
          ORDER BY a.id ASC";

$result = mysqli_query($conectar, $query);

$articulos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articulos[] = $row;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/admin.css"> 
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/reportes.css">
    <link rel="stylesheet" href="../../CSS/solicitarMantenimiento.css">
    <link rel="stylesheet" href="../../font/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Departamento</title>
</head>
<body>
    <header class="headerPanel">
    <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
    <div class="user-info">
        <i class="fas fa-user"></i>
        <span><?= htmlspecialchars($usuario) ?> | <?= htmlspecialchars($nombre_area) ?></span>
    </div>
</header>

    <!-- Menú lateral -->
    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()">
            <i class="fas fa-bars"></i> <!-- Icono de menú para cerrar -->
        </div>
        <ul class="sidebar-menu">
            <li><a href="../departamentos.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="articulosDepartamento.php"><i class="fas fa-boxes"></i> Artículos</a></li>
              <li><a href="solicitarMantenimiento.php"><i class="fas fa-book"></i> Solicitar Mantenimiento</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido principal -->
    <main class="main-content">
    <h1>
        <a href="departamentos.php" style="text-decoration: none;"></a>
        Artículos del Departamento
    </h1>

    <!-- Buscador -->
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input class="search-sm" type="text" id="searchInput" placeholder="Buscar artículo...">
    </div>

    <div class="table-container">
        <table class="scrollable-table" id="tablaArticulos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Serie</th>
                    <th>Modelo</th>
                    <th>Marca</th>
                    <th>Proveedor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articulos as $art): ?>
                    <tr>
                        <td><?= $art['id'] ?></td>
                        <td><?= htmlspecialchars($art['descripcion']) ?></td>
                        <td><?= htmlspecialchars($art['serie']) ?></td>
                        <td><?= htmlspecialchars($art['modelo']) ?></td>
                        <td><?= htmlspecialchars($art['marca']) ?></td>
                        <td><?= htmlspecialchars($art['proveedor']) ?></td>
                        <td>
                            <button class="btn-reporte" onclick='mostrarModal(<?= json_encode($art, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                <i class="fas fa-eye"></i> Ver
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
            <h2 class="modal-title"><i class="fas fa-info-circle"></i> Detalles del Artículo</h2>
            <div class="modal-details" id="modalDetails"></div>
            <div class="modal-buttons">
                <button class="btn-descarga" onclick="descargarPDF()">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Script de búsqueda con resaltado -->
<script>
    const input = document.getElementById('searchInput');
    if (input) {
        input.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tablaArticulos tbody tr');
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

                matches.push({ row, count });
                if (count > maxMatches) maxMatches = count;
            });

            matches.forEach(m => {
                m.row.style.display = (term === '' || (m.count === maxMatches && m.count > 0)) ? '' : 'none';
            });
        });
    }
</script>


    <!-- Script para el menú -->
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }
    </script>

    <script src="../../lib/jspdf.umd.min.js"></script>
<script src="../../lib/jspdf.plugin.autotable.min.js"></script>
<script>
    let datosArticulo = {};

    function mostrarModal(articulo) {
        datosArticulo = articulo;
        const detalles = document.getElementById('modalDetails');
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
            ["Área", articulo.area],
            ["Importe", "$" + (articulo.importe ? parseFloat(articulo.importe).toFixed(2) : "0.00")],
            ["Origen", articulo.origen],
            ["Fecha Registro", articulo.fecha_registro]
        ];

        let html = `<div class="modal-details-grid">`;
        campos.forEach(([label, value]) => {
            html += `
            <div class="modal-field">
                <span class="modal-label">${label}:</span>
                <span class="modal-value">${value || '<span class="modal-vacio">N/A</span>'}</span>
            </div>`;
        });
        html += `</div>`;
        detalles.innerHTML = html;
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function descargarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: "landscape",
            unit: "pt",
            format: "A4"
        });

        const logoUrl = '../../Logos/800px-ISSSTE_logo.png';
        const img = new Image();
        img.crossOrigin = '';
        img.src = logoUrl;

        img.onload = function () {
            doc.setFontSize(20);
            doc.setTextColor('#5c1434');
            doc.text("Reporte de Artículo", 40, 50);
            doc.addImage(img, "PNG", 700, 18, 120, 55);
            doc.setFillColor(245, 241, 233);
            doc.roundedRect(30, 70, 780, 400, 14, 14, 'F');

            const labels = [
                ["ID", datosArticulo.id],
                ["UR", datosArticulo.ur],
                ["No. Inventario", datosArticulo.no_inventario],
                ["CABM", datosArticulo.cabm],
                ["Descripción", datosArticulo.descripcion],
                ["Detalle", datosArticulo.descripcion_detalle],
                ["Partida Presupuestal", datosArticulo.partida_presupuestal],
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
                ["Área", datosArticulo.area],
                ["Importe", "$" + (datosArticulo.importe ? parseFloat(datosArticulo.importe).toFixed(2) : "0.00")],
                ["Origen", datosArticulo.origen],
                ["Fecha Registro", datosArticulo.fecha_registro]
            ];

            let startX = 55, startY = 90, colW = 240, rowH = 44, colCount = 0, maxCols = 3;
            doc.setFontSize(12);

            labels.forEach(([label, value], i) => {
                const x = startX + (colW * colCount);
                const y = startY + Math.floor(i / maxCols) * rowH;
                doc.setFillColor(240, 240, 240);
                doc.roundedRect(x, y, colW - 16, rowH - 8, 7, 7, 'F');
                doc.setDrawColor(140, 140, 140);
                doc.roundedRect(x, y, colW - 16, rowH - 8, 7, 7, 'S');
                doc.setFont(undefined, 'bold');
                doc.setTextColor('#5c1434');
                doc.text(`${label}:`, x + 12, y + 18);
                doc.setFont(undefined, 'normal');
                doc.setTextColor('#222');
                doc.text(String(value || ''), x + 12, y + 34, { maxWidth: colW - 40 });
                colCount++;
                if (colCount >= maxCols) colCount = 0;
            });

            doc.save(`articulo_${datosArticulo.id}.pdf`);
        };

        img.onerror = function () {
            alert('No se pudo cargar el logo del ISSSTE, el PDF se generará sin él.');
            // Puedes colocar aquí un fallback para generar sin logo si deseas
        };
    }
</script>

</body>
</html>