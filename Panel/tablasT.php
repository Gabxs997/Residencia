<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablas</title>
    <link rel="stylesheet" href="../CSS/admin.css"> 
    <link rel="stylesheet" href="../CSS/inventario.css">
    <link rel="stylesheet" href="../font/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    <!-- Menú lateral -->
    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()">
            <i class="fas fa-bars"></i> <!-- Icono de menú para cerrar -->
        </div>
        <ul class="sidebar-menu">
            <li><a href="tecnico.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="catalogoT.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../Panel/reportesT.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido de inventarios -->
    <main class="main-content">
    <h1>
            <a href="catalogoT.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            Tablas
        </h1><br><br>
    
    <div class="sections">
        <ul>
        <li><a href="tablasT/tablaArticulosT.php">Artículos</a></li>
            <li><a href="tablasT/tablaProveedoresT.php">Proveedores</a></li>
            <li><a href="tablasT/tablaPartidasT.php">Partidas</a></li>
            <li><a href="tablasT/tablaUbicacionesT.php">Áreas</a></li>
        </ul>
    </div>
</main>


    <!-- Script para el menú -->
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }
    </script>
</body>
</body>
</html>