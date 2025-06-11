<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Artículos</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span>Administrador</span>
        </div>
    </header>

    <!-- Menú lateral -->
    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()">
            <i class="fas fa-bars"></i> <!-- Icono de menú para cerrar -->
        </div>
        <ul class="sidebar-menu">
            <li><a href="../administrador.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido de inventarios -->
    <main class="main-content">
        <h1>
            <a href="../tablas.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once __DIR__ . '/../../config/conexion.php';
                    $query = "SELECT a.*, 
                 p.razon_social AS proveedor, 
                 u.nombre_area AS area
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
                            <td>
                                <span class="estado <?= strtolower($row['estado_bien']) ?>">
                                    <?= $row['estado_bien'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td><?= htmlspecialchars($row['rfc_responsable']) ?></td>
                            <td>$<?= number_format($row['importe'], 2) ?></td>
                            <td class="observaciones"><?= htmlspecialchars($row['observaciones']) ?></td>
                            <td><?= htmlspecialchars($row['origen']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                            <td class="acciones">
                                <a href="../actions/editarArticulo.php?id=<?= $row['id'] ?>" class="btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn-eliminar" onclick="return validar('../actions/eliminar.php?tabla=articulos&id=<?= $row['id'] ?>')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function editarArticulo(id) {
            window.location.href = 'editarArticulo.php?id=' + id;
        }

        function eliminarArticulo(id) {
            if (confirm('¿Estás seguro de eliminar este artículo?')) {
                window.location.href = '../actions/eliminarArticulo.php?id=' + id;
            }
        }
    </script>

    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#articlesTable tbody tr');
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
                if (searchTerm === '') {
                    item.row.style.display = '';
                } else {
                    item.row.style.display = (item.matchCount === maxMatches && item.matchCount > 0) ? '' : 'none';
                }
            });
        });
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

    <script>
        // Función para eliminar registros (usar en todas las tablas)
        function validar(url) {
            if (confirm('¿Estás seguro de eliminar este registro?')) {
                window.location.href = url;
            }
            return false; // Previene el comportamiento predeterminado del enlace
        }
    </script>
</body>

</html>