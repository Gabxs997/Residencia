<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../font/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Alta de Articulos</title>
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
            <li><a href="../administrador.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
            <li><a href="../inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
            <li><a href="../reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../crearUsuario.php"><i class="fas fa-user"></i>Usuarios</a></li>
            <li><a href="../cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Contenido de alta de artículos -->
    <main class="main-content">
        <h1>
            <a href="../catalogo.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            Alta de Artículos
        </h1><br><br>

        <!-- Formulario de Alta de Artículos -->
        <form action="../actions/guardarArticulo.php" method="POST">
            <h2>Datos de Artículo</h2><br><br>

            <!-- Sección 1: UR, No. Inventario, CABM -->
            <div class="contenedorArticulos">
                <div>
                    <label for="ur">UR:</label>
                    <input type="text" id="ur" name="ur" required>
                </div>
                <div>
                    <label for="noInventario">No. Inventario:</label>
                    <input type="text" id="noInventario" name="noInventario" required>
                </div>
                <div>
                    <label for="cabm">CABM:</label>
                    <input type="text" id="cabm" name="cabm" required>
                </div>
            </div>

            <!-- Sección 2: Descripción, Descripción Detalle, Partida Presupuestal -->
            <div class="contenedorArticulos">
                <div>
                    <label for="descripcion">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion" required>
                </div>
                <div>
                    <label for="descripcionDetalle">Descripción Detalle:</label>
                    <input type="text" id="descripcionDetalle" name="descripcionDetalle" required>
                </div>
                <div>
                    <label for="partidaPresupuestal">Partida Presupuestal:</label>
                    <select id="partidaPresupuestal" name="partidaPresupuestal" required>
                        <option value="">Selecciona una partida presupuestal</option>
                        <?php
                        include '../../config/conexion.php';
                        $sqlPresup = "SELECT DISTINCT numero_partida FROM partidas ORDER BY numero_partida ASC";
                        $resultadoPresup = mysqli_query($conectar, $sqlPresup);
                        while ($fila = mysqli_fetch_assoc($resultadoPresup)) {
                            echo "<option value='" . $fila['numero_partida'] . "'>" . $fila['numero_partida'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Sección 3: Partida Contable, Fecha Alta, Fecha Documento -->
            <div class="contenedorArticulos">
                <div>
                    <label for="partidaContable">Partida Contable:</label>
                    <select id="partidaContable" name="partidaContable" required>
                        <option value="">Selecciona una partida contable</option>
                        <?php
                        $sqlContable = "SELECT DISTINCT numero_subpartida FROM partidas ORDER BY numero_subpartida ASC";
                        $resultadoContable = mysqli_query($conectar, $sqlContable);
                        while ($fila = mysqli_fetch_assoc($resultadoContable)) {
                            echo "<option value='" . $fila['numero_subpartida'] . "'>" . $fila['numero_subpartida'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="fechaAlta">Fecha de Alta:</label>
                    <input type="date" id="fechaAlta" name="fechaAlta" required>
                </div>
                <div>
                    <label for="fechaDocumento">Fecha de Documento:</label>
                    <input type="date" id="fechaDocumento" name="fechaDocumento" required>
                </div>
            </div>

            <!-- Sección 4: Tipo de Bien (ocupará ancho completo) -->
            <div class="contenedorArticulos">
                <div class="full-width">
                    <label for="tipoBien">Tipo de Bien:</label>
                    <input type="text" id="tipoBien" name="tipoBien" required>
                </div>
            </div>

            <!-- Sección 5: No. Contrato, No. Factura, Proveedor -->
            <div class="contenedorDatosDocumento">
                <div>
                    <label for="noContrato">No. Contrato:</label>
                    <input type="text" id="noContrato" name="noContrato" required>
                </div>
                <div>
                    <label for="noFactura">No. Factura:</label>
                    <input type="text" id="noFactura" name="noFactura" required>
                </div>

                <div>
                    <label for="proveedor_id">Proveedor:</label>
                    <select id="proveedor_id" name="proveedor_id" required>
                        <option value="">Selecciona un proveedor</option>
                        <?php
                        include '../../config/conexion.php';
                        $sql = "SELECT id, razon_social FROM proveedores WHERE estado = 'Activo'";
                        $resultado = mysqli_query($conectar, $sql);
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo "<option value='" . $fila['id'] . "'>" . $fila['razon_social'] . "</option>";
                        }
                        ?>
                    </select>

                </div>
                </div>

                <!-- Sección 6: Serie, Modelo, Marca -->
                <div class="contenedorDatosDocumento">
                    <div>
                        <label for="serie">Serie:</label>
                        <input type="text" id="serie" name="serie" required>
                    </div>
                    <div>
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" required>
                    </div>
                    <div>
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" required>
                    </div>
                </div>

                <!-- Sección 7: Estado del Bien, Ubicación, RFC -->
                <div class="contenedorDatosDocumento">
                    <div>
                        <label for="estadoBien">Estado del Bien:</label>
                        <select id="estadoBien" name="estadoBien" required>
                            <option value="Bueno">Bueno</option>
                            <option value="Regular">Regular</option>
                            <option value="Malo">Malo</option>
                        </select>
                    </div>
                    <div>
                        <label for="ubicacion">Ubicación (Área):</label>
                        <select name="ubicacion" id="ubicacion" required>
                            <option value="">Selecciona un área</option>
                            <?php
                            $sqlUbicaciones = "SELECT id, nombre_area FROM ubicaciones ORDER BY nombre_area ASC";
                            $resUbicaciones = mysqli_query($conectar, $sqlUbicaciones);
                            if ($resUbicaciones) {
                                while ($fila = mysqli_fetch_assoc($resUbicaciones)) {
                                    echo "<option value='" . $fila['id'] . "'>" . htmlspecialchars($fila['nombre_area']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                </div>

                <!-- Sección 8: Importe, Observaciones (ancho completo) -->
                <div class="contenedorDatosDocumento">
                    <div>
                        <label for="importe">Importe: </label>
                        <input type="number" id="importe" name="importe" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="full-width">
                        <label for="observaciones">Observaciones:</label>
                        <textarea id="observaciones" name="observaciones"></textarea>
                    </div>
                    <div>
                        <label for="origen">Origen:</label>
                        <input type="text" id="origen" name="origen" required>
                    </div>
                </div>
<br><br>
                <!-- Botones Guardar + Resetear -->
                <div class="form-buttons">
                    <button type="submit"><i class="fas fa-save"></i> Guardar</button>
                    <button type="reset"><i class="fas fa-trash-alt"></i> Vacíar</button>
                </div>
        </form>
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

    <script>
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de resetear el formulario? Se perderán todos los datos.')) {
                e.preventDefault(); // Cancela el reset si el usuario elige "No"
            }
        });
    </script>

</body>

</html>