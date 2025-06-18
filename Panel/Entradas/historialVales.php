<?php
require_once __DIR__ . '/../../config/conexion.php';

$queryVales = "
    SELECT v.id, v.fecha_emision, u.nombre_area, v.archivo_pdf
    FROM vales_entrada v
    LEFT JOIN ubicaciones u ON v.area_id = u.id
    ORDER BY v.fecha_emision ASC
";
$resVales = mysqli_query($conectar, $queryVales);
?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Vales</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
</head>

<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="user-info"><i class="fas fa-user"></i><span>Administrador</span></div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <ul class="sidebar-menu">
            <li><a href="../administrador.php"><i class="fas fa-home"></i>Inicio</a></li>
            <li><a href="../catalogo.php"><i class="fas fa-book"></i>Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i>Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i>Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <?php
        $origen = $_GET['origen'] ?? 'normal'; // valor por defecto: normal
        $volverA = 'entradaNormal.php';
        $titulo = 'Historial de vales normales';
        $filtroArchivo = '%normal%';

        if ($origen === 'comodato') {
            $volverA = 'entradaComodato.php';
            $titulo = 'Historial de vales por comodato';
            $filtroArchivo = '%comodato%';
        } elseif ($origen === 'personal') {
            $volverA = 'articuloPersonal.php';
            $titulo = 'Historial de vales personales';
            $filtroArchivo = '%personal%';
        }

        $queryVales = "
    SELECT v.id, v.fecha_emision, u.nombre_area, v.archivo_pdf
    FROM vales_entrada v
    LEFT JOIN ubicaciones u ON v.area_id = u.id
    WHERE v.archivo_pdf LIKE '$filtroArchivo'
    ORDER BY v.fecha_emision DESC
";
        $resVales = mysqli_query($conectar, $queryVales);
        ?>
        <h1>
            <a href="<?= $volverA ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?= $titulo ?>
        </h1>


        <!-- 🔍 Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar en la tabla...">
        </div>
        <div class="table-container">
            <table class="scrollable-table" id="articlesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Área</th>
                        <th>Fecha de Emisión</th>
                        <th>Nombre del Vale</th>
                        <th>Documento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($vale = mysqli_fetch_assoc($resVales)): ?>
                        <tr>
                            <td><?= $vale['id'] ?></td>
                            <td><?= htmlspecialchars($vale['nombre_area']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($vale['fecha_emision'])) ?></td>
                            <td><?= htmlspecialchars($vale['archivo_pdf']) ?></td>
                            <td>
                                <?php $ruta = "../../doc/" . $vale['archivo_pdf']; ?>
                                <?php if (file_exists($ruta)): ?>
                                    <a href="<?= $ruta ?>" target="_blank" class="btn-ver">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                <?php else: ?>
                                    <span style="color: red;">No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div id="contenedorModal"></div>

            <script>
                function abrirModalEditar(id) {
                    fetch(`editarVale.php?id=${id}`)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenedorModal').innerHTML = html;
                        });
                }

                function cerrarModal() {
                    document.getElementById('contenedorModal').innerHTML = '';
                }
            </script>

        </div>
    </main>

    <script>
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
            const rows = document.querySelectorAll('#articlesTable tbody tr');

            rows.forEach(row => {
                let textMatch = false;
                const cells = row.querySelectorAll('td');

                // Excluir la última celda (columna del botón)
                for (let i = 0; i < cells.length - 1; i++) {
                    const cell = cells[i];
                    const originalText = cell.textContent;
                    cell.innerHTML = originalText;

                    if (searchTerm && originalText.toLowerCase().includes(searchTerm)) {
                        textMatch = true;
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
                    }
                }

                row.style.display = textMatch || searchTerm === '' ? '' : 'none';
            });
        });
    </script>


</body>

</html>