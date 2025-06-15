<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Ubicaciones</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
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

    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="sidebar-menu">
            <li><a href="administrador.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>
            <a href="../tablas.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            Tabla de Áreas
        </h1><br><br>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar ubicaciones..." class="search-input">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="locationsTable">
            <thead>
    <tr>
        <th>ID</th>
        <th>Área</th>
        <th>Coordinador</th>
        <th>RFC</th>
        <th>Teléfono</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    <?php
    require_once __DIR__ . '/../../config/conexion.php';

    $query = "SELECT * FROM ubicaciones ORDER BY id ASC";
    $result = mysqli_query($conectar, $query);

    while ($row = mysqli_fetch_assoc($result)):
    ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre_area']) ?></td>
            <td><?= htmlspecialchars($row['coordinador']) ?></td>
            <td><?= htmlspecialchars($row['rfc']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
            <td class="acciones">
                <a href="../actions/editarUbicacion.php?id=<?= $row['id'] ?>" class="btn-editar">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="btn-eliminar" onclick="return validar('../actions/eliminar.php?tabla=ubicaciones&id=<?= $row['id'] ?>')">
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
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#locationsTable tbody tr');
            let maxMatches = 0;
            const rowMatches = [];

            rows.forEach(row => {
                let matchCount = 0;
                const cells = row.querySelectorAll('td');

                // Guardar estado original
                const estadoBadge = row.querySelector('td:nth-child(5) .estado');
                const originalEstadoHTML = estadoBadge ? estadoBadge.outerHTML : '';

                // Quitar resaltados anteriores (excepto en estado)
                row.querySelectorAll('.highlight').forEach(el => {
                    if (!el.closest('.estado')) {
                        el.outerHTML = el.innerHTML;
                    }
                });

                // Restaurar estado original
                if (estadoBadge) estadoBadge.outerHTML = originalEstadoHTML;

                // Buscar coincidencias
                cells.forEach((cell, index) => {
                    if (index !== cells.length - 1) { // Excluir acciones
                        const text = cell.textContent.toLowerCase();
                        if (searchTerm !== '') {
                            const regex = new RegExp(searchTerm, 'gi');
                            const matches = text.match(regex);
                            if (matches) {
                                matchCount += matches.length;
                                // Resaltar (excepto en estado)
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
                if (matchCount > maxMatches) maxMatches = matchCount;
            });

            // Mostrar resultados
            rowMatches.forEach(item => {
                item.row.style.display = (searchTerm === '' || (item.matchCount === maxMatches && item.matchCount > 0)) ?
                    '' :
                    'none';
            });
        });

        function validar(url) {
            if (confirm('¿Estás seguro de eliminar este registro?')) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</body>
</html>