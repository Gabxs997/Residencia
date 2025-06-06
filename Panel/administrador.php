<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Inventario</title>
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
            <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../Panel/catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../Panel/inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
            <li><a href="../Panel/reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../Panel/crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido principal -->
    <main class="main-content">
        <h1>¡Bienvenido!</h1>
        <p>Este es un programa con el propósito de administrar activos médicos del Hospital Regional ISSSTE "Elvia Carrillo Puerto". Para iniciar, seleccione una de las opciones del menú izquierdo.</p>
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
</html>