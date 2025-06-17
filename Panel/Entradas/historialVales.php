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
    <h1><a href="entradaNormal.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Historial de vales normales</h1><br><br>

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
                        <?php
                        $ruta = "../../doc/" . $vale['archivo_pdf'];
                        if (file_exists($ruta)): ?>
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
</body>
</html>
