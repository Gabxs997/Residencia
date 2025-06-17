<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico</title>
    <link rel="stylesheet" href="../CSS/tecnico.css"> 
    <link rel="stylesheet" href="../font/css/all.min.css">
</head>
<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="user-info">
            <i class="fas fa-user"></i> 
            <span>Técnico</span>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <ul class="sidebar-menu">
            <li><a href="#"><i class="fas fa-home"></i>Inicio</a></li>
            <li><a href="../Panel/catalogoT.php"><i class="fas fa-book"></i>Catálogo</a></li>
            <li><a href="../Panel/reportesT.php"><i class="fas fa-file-alt"></i>Reportes</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Bienvenido, Técnico</h1>
        <p>Accede a las funciones disponibles desde el menú lateral.</p>
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
