<?php
require_once __DIR__ . '/../../config/conexion.php';

// Obtener listado de áreas
$areas = [];
$res = mysqli_query($conectar, "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area");
while ($row = mysqli_fetch_assoc($res)) {
    $areas[] = $row;
}

// Valor inicial -1 para evitar mostrar la tabla sin selección
$areaSeleccionada = isset($_GET['area']) && is_numeric($_GET['area']) ? intval($_GET['area']) : -1;
?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vales de Entrada Normales</title>
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
        <h1><a href="entradas.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Vales de entrada (normales)</h1><br><br>

        <form method="get" class="form-area-select">
            <label for="area" class="area-label">Área:</label>
            <select name="area" id="area" class="select-area" onchange="this.form.submit()">
                <option value="" disabled <?= $areaSeleccionada === -1 ? 'selected' : '' ?>>Selecciona un área</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id'] ?>" <?= $areaSeleccionada == $area['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($area['nombre_area']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </form>
        <br><br>

        <?php if ($areaSeleccionada > 0): ?>
            <div class="usuarios-toolbar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Buscar artículos..." class="search-input">
                </div>
                <div class="btn-container">
                    <button class="btn-generar" onclick="generarVale()">
                        <i class="fas fa-file-alt"></i> Generar Vale
                    </button>
                    <a href="historialVales.php" class="btn-historial">
                        <i class="fas fa-clock"></i> Historial de Vales
                    </a>
                </div>

            </div>

            <div class="table-container">
                <table class="scrollable-table" id="articlesTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Serie</th>
                            <th>Área</th>
                            <th>Proveedor</th>
                            <th>Fecha de registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT a.id, a.descripcion, a.marca, a.modelo, a.serie,
                            u.nombre_area AS area, p.razon_social AS proveedor, a.fecha_registro
                            FROM articulos a
                            LEFT JOIN ubicaciones u ON a.ubicacion = u.id
                            LEFT JOIN proveedores p ON a.proveedor_id = p.id
                            WHERE a.ubicacion = $areaSeleccionada
                            ORDER BY a.id DESC";

                        $result = mysqli_query($conectar, $sql);

                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        name="articulos[]"
                                        value="<?= $row['id'] ?>"
                                        data-proveedor="<?= htmlspecialchars($row['proveedor']) ?>">
                                </td>

                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                <td><?= htmlspecialchars($row['marca']) ?></td>
                                <td><?= htmlspecialchars($row['modelo']) ?></td>
                                <td><?= htmlspecialchars($row['serie']) ?></td>
                                <td><?= htmlspecialchars($row['area']) ?></td>
                                <td><?= htmlspecialchars($row['proveedor']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $infoCoordinador = ['coordinador' => '', 'rfc' => '', 'contacto' => '', 'nombre_area' => ''];

            $areaID = $areaSeleccionada;
            $queryCoord = "SELECT coordinador, rfc, nombre_area FROM ubicaciones WHERE id = $areaID LIMIT 1";
            $resCoord = mysqli_query($conectar, $queryCoord);
            if ($fila = mysqli_fetch_assoc($resCoord)) {
                $infoCoordinador['coordinador'] = $fila['coordinador'];
                $infoCoordinador['rfc'] = $fila['rfc'];
                $infoCoordinador['nombre_area'] = $fila['nombre_area'];
            }

            $queryContacto = "
    SELECT cp.nombre
    FROM articulos a
    INNER JOIN contactos_proveedores cp ON cp.proveedor_id = a.proveedor_id
    WHERE a.ubicacion = $areaID
    ORDER BY a.id DESC LIMIT 1";
            $resContacto = mysqli_query($conectar, $queryContacto);
            $tieneContacto = '0';

            if ($resContacto && mysqli_num_rows($resContacto) > 0) {
                $fila = mysqli_fetch_assoc($resContacto);
                $infoCoordinador['contacto'] = $fila['nombre'];
                $tieneContacto = '1';
            } else {
                $infoCoordinador['contacto'] = '';
            }

            ?>

            <div class="modal-overlay" id="modalVale" style="display:none;">
                <div class="modal-container">
                    <span class="modal-close" onclick="cerrarModal()">
                        <i class="fas fa-times"></i>
                    </span>
                    <h2 class="modal-title">Generar Vale de Entrada</h2>

                    <form id="formVale" method="POST" action="generarValeNormal.php">
                        <input type="hidden" name="area_id" value="<?= htmlspecialchars($_GET['area'] ?? '') ?>">
                        <input type="hidden" name="articulos_ids" id="articulos_ids">
                        <input type="hidden" id="tieneContacto" value="<?= $tieneContacto ?>">
                        <input type="hidden" name="proveedor" id="inputProveedor">


                        <?php
                        
                        // Buscar jefe de activo fijo desde ubicaciones
                        $jefeActivoFijo = '';
                        $resJefeFijo = mysqli_query($conectar, "SELECT coordinador FROM ubicaciones WHERE UPPER(nombre_area) = 'ACTIVO FIJO' LIMIT 1");
                        if ($resJefeFijo && mysqli_num_rows($resJefeFijo) > 0) {
                            $jefeActivoFijo = mysqli_fetch_assoc($resJefeFijo)['coordinador'];
                        } else {
                            echo "<script>alert('⚠️ No existe el área ACTIVO FIJO. Por favor, créala en el catálogo de ubicaciones antes de generar un vale.'); window.location.href = '../catalogo.php';</script>";
                            exit;
                        }
                        ?>

                        <div class="modal-field">
                            <label>Jefe de Activo Fijo:</label>
                            <p class="modal-static"><?= htmlspecialchars($jefeActivoFijo) ?></p>
                            <input type="hidden" name="jefe_activo_fijo" value="<?= htmlspecialchars($jefeActivoFijo) ?>">
                        </div>




                        <div class="modal-field">
                            <label>Coordinador del Área:</label>
                            <p class="modal-static"><?= htmlspecialchars(string: $infoCoordinador['coordinador']) ?></p>
                        </div>

                        <div class="modal-field">
                            <label>RFC del Coordinador:</label>
                            <p class="modal-static"><?= htmlspecialchars($infoCoordinador['rfc']) ?></p>
                        </div>

                        <div class="modal-field">
                            <label>Área:</label>
                            <p class="modal-static"><?= htmlspecialchars($infoCoordinador['nombre_area']) ?></p>
                        </div>

                        <div class="modal-field">
                            <label>Contacto del Proveedor:</label>
                            <p class="modal-static"><?= htmlspecialchars($infoCoordinador['contacto']) ?></p>
                        </div>

                        <div class="modal-field">
                            <label>Proveedor:</label>
                            <p class="modal-static" id="nombreProveedor"></p>
                        </div>


                        <div class="form-buttons">
                            <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                            <button type="submit" class="btn-descarga">Confirmar y Generar</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <p style="text-align:center; font-weight:bold; margin-top: 40px;">
                Por favor, seleccione un área para mostrar los artículos.
            </p>
        <?php endif; ?>
    </main>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#articlesTable tbody input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function generarVale() {
            const areaSelect = document.getElementById('area');
            const areaSeleccionada = areaSelect.value;

            if (!areaSeleccionada || areaSeleccionada === "-1") {
                alert("Primero seleccione un área.");
                return;
            }

            const seleccionados = [...document.querySelectorAll('input[name="articulos[]"]:checked')];
            if (seleccionados.length === 0) {
                alert("Seleccione al menos un artículo.");
                return;
            }

            const tieneContacto = document.getElementById('tieneContacto')?.value;
            if (tieneContacto === '0') {
                alert("El proveedor asociado no tiene un contacto registrado. Por favor, actualice el proveedor o agregue un contacto.");
                return;
            }

            // 🔎 Verificar si todos los proveedores son iguales
            const proveedores = seleccionados.map(cb => cb.getAttribute('data-proveedor'));
            const proveedorUnico = proveedores.every(p => p === proveedores[0]);

            if (!proveedorUnico) {
                alert("⚠️ Solo se puede generar un vale con artículos del mismo proveedor.");
                return;
            }

            // ✅ Todo bien, continuar
            const ids = seleccionados.map(cb => cb.value);
            document.getElementById('articulos_ids').value = ids.join(',');

            const proveedor = proveedores[0];
            document.getElementById('inputProveedor').value = proveedor;

            const proveedorTexto = document.getElementById('nombreProveedor');
            if (proveedorTexto) proveedorTexto.textContent = proveedor;

            document.getElementById('modalVale').style.display = 'flex';
        }




        //Script de cerrar modal
        function cerrarModal() {
            document.getElementById('modalVale').style.display = 'none';
        }

        document.getElementById('searchInput')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#articlesTable tbody tr');

            rows.forEach(row => {
                let textMatch = false;
                row.querySelectorAll('td').forEach(cell => {
                    const originalText = cell.textContent;
                    cell.innerHTML = originalText;
                    if (searchTerm && originalText.toLowerCase().includes(searchTerm)) {
                        textMatch = true;
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
                    }
                });
                row.style.display = textMatch || searchTerm === '' ? '' : 'none';
            });
        });
    </script>
</body>

</html>