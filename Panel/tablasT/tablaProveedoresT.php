<?php
require_once __DIR__ . '/../../config/conexion.php';
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Proveedores</title>
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
            Tabla de Proveedores
        </h1><br><br>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar proveedores..." class="search-input">
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="providersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Razón Social</th>
                        <th>Dirección</th>
                        <th>Teléfono 1</th>
                        <th>Teléfono 2</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Contacto</th>
                        <th>Teléfono Contacto</th>
                        <th>Email Contacto</th>
                        <th>Estado Contacto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT p.*, 
                                c.nombre as contacto_nombre, 
                                c.telefono1 as contacto_telefono, 
                                c.email as contacto_email,
                                c.estado as contacto_estado
                              FROM proveedores p
                              LEFT JOIN contactos_proveedores c ON p.id = c.proveedor_id
                              ORDER BY p.id ASC";
                    $result = mysqli_query($conectar, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>" . htmlspecialchars($row['razon_social']) . "</td>
                            <td>" . htmlspecialchars($row['direccion']) . "</td>
                            <td>" . htmlspecialchars($row['telefono1']) . "</td>
                            <td>" . htmlspecialchars($row['telefono2'] ?? 'N/A') . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td><span class='estado " . strtolower($row['estado']) . "'>{$row['estado']}</span></td>
                            <td>" . date('d/m/Y H:i', strtotime($row['fecha_creacion'])) . "</td>
                            <td>" . htmlspecialchars($row['contacto_nombre'] ?? 'N/A') . "</td>
                            <td>" . htmlspecialchars($row['contacto_telefono'] ?? 'N/A') . "</td>
                            <td>" . htmlspecialchars($row['contacto_email'] ?? 'N/A') . "</td>
                            <td>" . (isset($row['contacto_estado']) ? "<span class='estado " . strtolower($row['contacto_estado']) . "-contacto'>{$row['contacto_estado']}</span>" : "N/A") . "</td>
                        </tr>";
                    }
                    ?>
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
            const rows = document.querySelectorAll('#providersTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
