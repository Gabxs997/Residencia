<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Artículos - Técnico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span>Técnico</span>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../tecnico.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogoT.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>
            <a href="../tablasT.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            Tabla de Artículos
        </h1><br><br>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar artículos..." class="search-input">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="articlesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>UR</th>
                        <th>No. Inventario</th>
                        <th>CABM</th>
                        <th>Descripción</th>
                        <th>Desc. Detalle</th>
                        <th>Part. Presup.</th>
                        <th>Part. Contable</th>
                        <th>F. Alta</th>
                        <th>F. Documento</th>
                        <th>Tipo Bien</th>
                        <th>No. Contrato</th>
                        <th>No. Factura</th>
                        <th>Proveedor</th>
                        <th>Serie</th>
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Estado</th>
                        <th>Área</th>
                        <th>RFC</th>
                        <th>Importe</th>
                        <th>Detalles</th>
                        <th>Origen</th>
                        <th>F. Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once __DIR__ . '/../../config/conexion.php';
                    $query = "SELECT a.*, p.razon_social AS proveedor, u.nombre_area AS area
                              FROM articulos a
                              LEFT JOIN proveedores p ON a.proveedor_id = p.id
                              LEFT JOIN ubicaciones u ON a.ubicacion = u.id
                              ORDER BY a.id ASC";
                    $result = mysqli_query($conectar, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['ur']) ?></td>
                        <td><?= htmlspecialchars($row['no_inventario']) ?></td>
                        <td><?= htmlspecialchars($row['cabm']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion_detalle']) ?></td>
                        <td><?= htmlspecialchars($row['partida_presupuestal']) ?></td>
                        <td><?= htmlspecialchars($row['partida_contable']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_alta'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_documento'])) ?></td>
                        <td><?= htmlspecialchars($row['tipo_bien']) ?></td>
                        <td><?= htmlspecialchars($row['no_contrato']) ?></td>
                        <td><?= htmlspecialchars($row['no_factura']) ?></td>
                        <td><?= htmlspecialchars($row['proveedor']) ?></td>
                        <td><?= htmlspecialchars($row['serie']) ?></td>
                        <td><?= htmlspecialchars($row['modelo']) ?></td>
                        <td><?= htmlspecialchars($row['marca']) ?></td>
                        <td><span class="estado <?= strtolower($row['estado_bien']) ?>"><?= $row['estado_bien'] ?></span></td>
                        <td><?= htmlspecialchars($row['area']) ?></td>
                        <td><?= htmlspecialchars($row['rfc_responsable']) ?></td>
                        <td>$<?= number_format($row['importe'], 2) ?></td>
                        <td><?= htmlspecialchars($row['observaciones']) ?></td>
                        <td><?= htmlspecialchars($row['origen']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
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

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#articlesTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
