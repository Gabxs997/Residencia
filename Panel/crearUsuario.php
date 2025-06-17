<?php
require_once __DIR__ . '/../config/conexion.php';
?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" href="../CSS/admin.css">
    <link rel="stylesheet" href="../CSS/tablas.css">
    <link rel="stylesheet" href="../CSS/modal.css">
    <link rel="stylesheet" href="../font/css/all.min.css">
</head>

<body>
    <header class="headerPanel">
        <div class="menu-icon" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="user-info"><i class="fas fa-user"></i><span>Administrador</span></div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="menu-icon-close" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <ul class="sidebar-menu">
            <li><a href="administrador.php"><i class="fas fa-home"></i>Inicio</a></li>
            <li><a href="../Panel/catalogo.php"><i class="fas fa-book"></i>Catálogo</a></li>
            <li><a href="../Panel/inventario.php"><i class="fas fa-boxes"></i>Inventario</a></li>
            <li><a href="../Panel/reportes.php"><i class="fas fa-file-alt"></i>Reportes</a></li>
            <li><a href="../Panel/crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Control de usuarios</h1><br><br>
        <div class="usuarios-toolbar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input class="search-input" type="text" id="searchInput" placeholder="Buscar usuario...">
            </div>
            <div>
                <button class="btn btn-save" onclick="abrirModalUsuario()">Crear Usuario</button>
            </div>
        </div>


        <!-- Modal para crear usuario -->
        <div class="modal-overlay" id="modalUsuario" style="display: none;">
            <div class="modal-container">
                <span class="modal-close" onclick="cerrarModalUsuario()">&times;</span>
                <h2 class="modal-title">Crear Nuevo Usuario</h2>
                <form action="actions/guardarUsuario.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="usuario">Nombre de usuario:</label>
                            <input type="text" id="usuario" name="usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="area">Área (Departamento):</label>
                            <select id="area" name="area_id" required>
                                <?php
                                $areas = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area");
                                while ($area = mysqli_fetch_assoc($areas)) {
                                    echo "<option value='{$area['id']}'>" . htmlspecialchars($area['nombre_area']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">Guardar</button>
                        <button type="button" class="btn btn-cancel" onclick="cerrarModalUsuario()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="table-container">
            <table class="scrollable-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Contraseña (encriptada)</th>
                        <th>Área</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usuarios = mysqli_query($conectar, "
                SELECT u.id, u.usuario, u.contrasena, a.nombre_area
                FROM usuarios_departamento u
                LEFT JOIN ubicaciones a ON u.area_id = a.id
                ORDER BY u.id ASC
            ");
                    while ($usuario = mysqli_fetch_assoc($usuarios)):
                    ?>
                        <tr>
                            <td><?= $usuario['id'] ?></td>
                            <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['contrasena']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_area']) ?></td>
                            <td class="acciones">
                                <a href="actions/editarUsuario.php?id=<?= $usuario['id'] ?>" class="btn-editar"><i class="fas fa-edit"></i> Editar</a>
                                <a href="actions/eliminar.php?eliminar_usuario=<?= $usuario['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    <i class="fas fa-trash-alt"></i> Eliminar
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
    </script>
    <!-- Scripts del modal -->
    <script>
        function abrirModalUsuario() {
            document.getElementById('modalUsuario').style.display = 'flex';
        }

        function cerrarModalUsuario() {
            document.getElementById('modalUsuario').style.display = 'none';
        }
    </script>

    <script>
        // Buscador con resaltado
        const input = document.getElementById('searchInput');
        if (input) {
            input.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('.scrollable-table tbody tr');
                let maxMatches = 0;
                const matches = [];

                rows.forEach(row => {
                    let count = 0;
                    const cells = row.querySelectorAll('td');

                    row.querySelectorAll('.highlight').forEach(el => el.outerHTML = el.innerHTML);

                    cells.forEach(cell => {
                        const text = cell.textContent.toLowerCase();
                        if (term !== '') {
                            const regex = new RegExp(term, 'gi');
                            const found = text.match(regex);
                            if (found) {
                                count += found.length;
                                cell.innerHTML = cell.textContent.replace(regex, m => `<span class="highlight">${m}</span>`);
                            }
                        }
                    });

                    matches.push({
                        row,
                        count
                    });
                    if (count > maxMatches) maxMatches = count;
                });

                matches.forEach(m => {
                    m.row.style.display = (term === '' || (m.count === maxMatches && m.count > 0)) ? '' : 'none';
                });
            });
        }
    </script>

</body>

</html>