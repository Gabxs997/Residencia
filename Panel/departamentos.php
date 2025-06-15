<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

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
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin.css"> 
    <link rel="stylesheet" href="../font/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <li><a href="departamentos.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="departamento/articulosDepartamento.php"><i class="fas fa-boxes"></i> Artículos</a></li>
              <li><a href="departamento/solicitarMantenimiento.php"><i class="fas fa-book"></i> Solicitar Mantenimiento</a></li>
            <li><a href="cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido principal -->
    <main class="main-content">
        <h1>¡Bienvenido!</h1>
        <p>Este es el panel del departamento, selecciona una opción del menu izquierdo.</p>
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