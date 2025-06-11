<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/inventario.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Alta de Ubicación</title>
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
            <i class="fas fa-bars"></i>
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

    <!-- Contenido principal -->
    <main class="main-content">
        <h1>
            <a href="../catalogo.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            Alta de Área
        </h1><br><br>

        <!-- Formulario -->
        <form action="../actions/guardarUbicacion.php" method="POST">
            <!-- Sección: Datos de Ubicación -->
            <h2>Datos de Área</h2><br><br>

            <div class="contenedorUbicacion">
                <!-- Columna 1 -->
                <div>
                    <label for="nombreArea">Nombre del área:</label>
                    <input type="text" id="nombreArea" name="nombreArea" required>
                </div>

                <!-- Columna 2 -->
                <div>
                    <label for="coordinador">Nombre del coordinador:</label>
                    <input type="text" id="coordinador" name="coordinador" required>
                </div>
            </div>

            <div class="contenedorUbicacion">
                <div>
                    <label for="rfc">RFC del coordinador:</label>
                    <input type="text" id="rfc" name="rfc" maxlength="13" required>
                </div>

                <div>
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
            </div>

            <!-- Botones -->
            <div class="form-buttons">
                <button type="submit"><i class="fas fa-save"></i> Guardar</button>
                <button type="reset"><i class="fas fa-trash-alt"></i> Vaciar</button>
            </div>
        </form>
    </main>

    <!-- Scripts -->
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de vaciar el formulario?')) {
                e.preventDefault();
            }
        });
    </script>
    <script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const telefono = document.getElementById('telefono').value.trim().toLowerCase();

        const esValido = telefono === 'n/a' || /^[0-9]{10}$/.test(telefono);

        if (!esValido) {
            alert("El teléfono debe tener 10 dígitos o escribir 'N/A'");
            e.preventDefault();
        }
    });
</script>

</body>
</html>
