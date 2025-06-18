<?php
require_once __DIR__ . '/../../config/conexion.php';

$articulos = mysqli_query($conectar, "
    SELECT ap.id, ap.nombre_persona, ap.numero_empleado, u.nombre_area, ap.descripcion_personal, ap.fecha_registro
    FROM articulos_personales ap
    INNER JOIN ubicaciones u ON ap.area_id = u.id
    ORDER BY ap.id DESC
");



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validar_area']) && isset($_POST['area_id'])) {
    require_once __DIR__ . '/../../config/conexion.php';
    $id = intval($_POST['area_id']);
    $res = mysqli_query($conectar, "SELECT eliminado FROM ubicaciones WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    echo json_encode(['inactiva' => $row && $row['eliminado'] == 1]);
    exit;
}


?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Artículos Personales</title>
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/modal.css">
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
            <li><a href="../catalogo.php"><i class="fas fa-book"></i>Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i>Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i>Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>
            <a href="entradas.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            Artículos Personales
        </h1>
        <br><br>

        <div class="usuarios-toolbar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input class="search-input" type="text" id="searchInput" placeholder="Buscar artículo...">
            </div>
            <!-- Modal para crear artículo personal -->
            <div class="modal-overlay" id="modalArticuloPersonal" style="display: none;">
                <div class="modal-container">
                    <span class="modal-close" onclick="cerrarModalArticulo()">&times;</span>
                    <h2 class="modal-title">Crear Artículo Personal</h2>
                    <form action="guardarArticuloPersonal.php" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombre_persona">Nombre de la Persona:</label>
                                <input type="text" id="nombre_persona" name="nombre_persona" required>
                            </div>

                            <div class="modal-field">
                                <label for="numero_empleado">Número de empleado:</label>
                                <input type="text" id="numero_empleado" name="numero_empleado" required placeholder="Ej. 12345">
                            </div>

                            <div class="form-group">
                                <label for="area_id">Área (Departamento):</label>
                                <select id="area_id" name="area_id" required>
                                    <option value="" disabled selected>Selecciona un área</option>
                                    <?php
                                    $areas = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area");
                                    while ($a = mysqli_fetch_assoc($areas)) {
                                        echo "<option value='{$a['id']}'>" . htmlspecialchars($a['nombre_area']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="descripcion_personal">Descripción del Artículo:</label>
                                <textarea id="descripcion_personal" name="descripcion_personal" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-save">Guardar</button>
                            <button type="button" class="btn btn-cancel" onclick="cerrarModalArticulo()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <button class="btn btn-save" onclick="abrirModalArticulo()">
                    <i class="fas fa-plus"></i> Crear Artículo Personal
                </button>
                <a href="historialVales.php?origen=personal" class="btn btn-historial">
                    <i class="fas fa-file-alt"></i> Historial de Vales
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="scrollable-table" id="articlesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Número de empleado</th>
                        <th>Área</th>
                        <th>Descripción</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($art = mysqli_fetch_assoc($articulos)): ?>
                        <tr>
                            <td><?= $art['id'] ?></td>
                            <td><?= htmlspecialchars($art['nombre_persona']) ?></td>
                            <td><?= htmlspecialchars($art['numero_empleado']) ?></td> <!-- NUEVO -->
                            <td><?= htmlspecialchars($art['nombre_area']) ?></td>
                            <td><?= htmlspecialchars($art['descripcion_personal']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($art['fecha_registro'])) ?></td>
                            <td class="acciones">
                                <button class="btn-verde btn-accion" onclick="generarVale(<?= $art['id'] ?>)">Generar vale</button>
                                <button class="btn-editar btn-accion" onclick="editarArticulo(<?= $art['id'] ?>)">Editar</button>
                                <button class="btn-rojo btn-accion" onclick="eliminarArticulo(<?= $art['id'] ?>)">Eliminar</button>
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
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#articlesTable tbody tr');
            rows.forEach(row => {
                let match = false;
                row.querySelectorAll('td').forEach(cell => {
                    const text = cell.textContent.toLowerCase();
                    match = match || text.includes(term);
                });
                row.style.display = match ? '' : 'none';
            });
        });
    </script>

    <script>
        function abrirModalArticulo() {
            document.getElementById('modalArticuloPersonal').style.display = 'flex';
        }

        function cerrarModalArticulo() {
            document.getElementById('modalArticuloPersonal').style.display = 'none';
        }
    </script>

    <script>
        const selectArea = document.getElementById('area_id');
        const btnGuardar = document.querySelector('#modalArticuloPersonal .btn-save');

        selectArea.addEventListener('change', function() {
            const areaId = this.value;

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'validar_area=1&area_id=' + encodeURIComponent(areaId)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.inactiva) {
                        alert('⚠️ Esta área está inactiva. Debes darle de alta de nuevo para reactivarla.');
                        window.location.href = '../forms/altaUbicacion.php';
                    }

                });
        });
    </script>
    <script>
        function generarVale(id) {
            window.location.href = 'generarValePersonal.php?id=' + id;
        }

        function editarArticulo(id) {
            // Puedes implementar un modal o redirigir a una vista de edición
            window.location.href = 'editarArticuloPersonal.php?id=' + id;
        }

        function eliminarArticulo(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este artículo?')) {
                window.location.href = 'eliminarArticuloPersonal.php?id=' + id;
            }
        }
    </script>


</body>

</html>