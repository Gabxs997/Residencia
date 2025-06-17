<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../font/css/all.min.css">
    <title>Alta de Proveedores</title>
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
        <h1><a href="../catalogo.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Alta de Proveedores</h1><br><br>

        <form action="../actions/guardarProveedor.php" method="POST">
            <h2>Datos del Proveedor</h2><br><br>

            <div class="contenedorProveedores">
                <div>
                    <label for="razonSocial">Razón Social:</label>
                    <input type="text" id="razonSocial" name="razonSocial" required>
                </div>
                <div>
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
            </div>

            <div class="contenedorProveedores">
                <div>
                    <label for="telefono1">Teléfono 1:</label>
                    <input type="tel" id="telefono1" name="telefono1" required>
                </div>
                <div>
                    <label for="telefono2">Teléfono 2:</label>
                    <input type="tel" id="telefono2" name="telefono2">
                </div>
            </div>

            <div class="contenedorProveedores">
                <div>
                    <label for="email">Correo Electrónico:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="email" name="email" required>
                    </div>
                </div>
                <div>
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <h2>Datos del contacto</h2>

            <div class="contenedorProveedores">
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombreContacto" name="nombreContacto">
                </div>
                <div>
                    <label for="direccionContacto">Dirección:</label>
                    <input type="text" id="direccionContacto" name="direccionContacto">
                </div>
            </div>

            <div class="contenedorProveedores">
                <div>
                    <label for="telefonoContacto1">Teléfono 1:</label>
                    <input type="tel" id="telefonoContacto1" name="telefonoContacto1">
                </div>
                <div>
                    <label for="telefonoContacto2">Teléfono 2:</label>
                    <input type="tel" id="telefonoContacto2" name="telefonoContacto2">
                </div>
            </div>

            <div class="contenedorProveedores">
                <div>
                    <label for="emailContacto">Correo Electrónico:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="emailContacto" name="emailContacto">
                    </div>
                </div>
                <div>
                    <label for="estadoContacto">Estado:</label>
                    <select id="estadoContacto" name="estadoContacto">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit"><i class="fas fa-save"></i> Guardar</button>
                <button type="reset"><i class="fas fa-trash-alt"></i> Vaciar</button>
            </div>
        </form>
    </main>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        function ponerNA(idCampo) {
            document.getElementById(idCampo).value = "N/A";
        }

        // Validación personalizada al enviar
        document.querySelector('form').addEventListener('submit', function (e) {
            const emailProveedor = document.getElementById('email').value.trim();
            const emailContacto = document.getElementById('emailContacto').value.trim();

            const esValido = (correo) => {
                return correo.toLowerCase() === "n/a" || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
            };

            if (!esValido(emailProveedor)) {
                alert("El correo del proveedor debe ser un correo válido o 'N/A'");
                e.preventDefault();
                return;
            }

            if (!esValido(emailContacto)) {
                alert("El correo del contacto debe ser un correo válido o 'N/A'");
                e.preventDefault();
                return;
            }
        });

        document.querySelector('button[type="reset"]').addEventListener('click', function (e) {
            if (!confirm('¿Estás seguro de vaciar el formulario?')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
