<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Partidas</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
            <li><a href="../Panel/reportesT.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>
            <a href="../tablasT.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            Tabla de Partidas
        </h1><br><br>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar partidas..." class="search-input">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="partidasTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Partida Presupuestal</th>
                        <th>Nombre Partida</th>
                        <th>Partida Contable</th>
                        <th>Nombre Subpartida</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once __DIR__ . '/../../config/conexion.php';
                    $query = "SELECT * FROM partidas ORDER BY numero_partida, numero_subpartida ASC";
                    $result = mysqli_query($conectar, $query);

                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['numero_partida']) ?></td>
                            <td><?= htmlspecialchars($row['nombre_partida']) ?></td>
                            <td><?= htmlspecialchars($row['numero_subpartida']) ?></td>
                            <td><?= htmlspecialchars($row['nombre_subpartida']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
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

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#partidasTable tbody tr');
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
    </script>
</body>
</html>
