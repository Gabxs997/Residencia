<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

if (!isset($_SESSION['area_id']) || !isset($_SESSION['usuario'])) {
    header('Location: ../../Login/loginUsuario.php');
    exit;
}

$area_id = $_SESSION['area_id'];
$usuario = $_SESSION['usuario'];

// Obtener nombre del área
$resArea = mysqli_query($conectar, "SELECT nombre_area FROM ubicaciones WHERE id = $area_id LIMIT 1");
$area = mysqli_fetch_assoc($resArea);
$nombre_area = $area ? $area['nombre_area'] : 'Área desconocida';

// Obtener artículos del área
$articulos = [];
$resArt = mysqli_query($conectar, "
    SELECT a.id, a.descripcion, u.nombre_area,
        (SELECT estatus FROM solicitudes_mantenimiento sm WHERE sm.articulo_id = a.id ORDER BY sm.id DESC LIMIT 1) AS estatus
    FROM articulos a
    INNER JOIN ubicaciones u ON a.ubicacion = u.id
    WHERE a.ubicacion = $area_id
");
while ($row = mysqli_fetch_assoc($resArt)) $articulos[] = $row;

function mostrarEstatus($valor)
{
    return match ($valor) {
        '1' => "<span class='badge-sm badge-proceso'>En proceso</span>",
        '2' => "<span class='badge-sm badge-terminado'>Terminado</span>",
        default => "<span class='badge-sm badge-pendiente'>Pendiente</span>",
    };
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitar Mantenimiento</title>
    <link rel="stylesheet" href="../../CSS/tablas.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="../../CSS/solicitarMantenimiento.css">

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
            <li><a href="../reportesT.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
            <li><a href="../Panel/cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1><a href="../reportesT.php" class="back-btn"><i class="fas fa-arrow-left"></i></a> Solicitar mantenimiento</h1> <br><br><br><br>

        <div class="usuario-area-info">
            <p><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION['usuario']) ?></p>
            <p><strong>Área:</strong> <?= htmlspecialchars($nombre_area) ?></p>
        </div>

        <br>
        <?php if ($area_id > 0): ?>
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input class="search-sm" type="text" id="searchInput" placeholder="Buscar artículo...">
            </div>

            <div class="table-container-sm">
                <table id="tablaArticulos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Área</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articulos as $art): ?>
                            <tr>
                                <td><?= $art['id'] ?></td>
                                <td><?= htmlspecialchars($art['descripcion']) ?></td>
                                <td><?= htmlspecialchars($art['nombre_area']) ?></td>
                                <td><?= mostrarEstatus($art['estatus']) ?></td>
                                <td>
                                    <?php
                                    // Verificar si hay solicitud activa para este artículo
                                    $articulo_id = $art['id'];
                                    $solicitud = mysqli_query($conectar, "SELECT id FROM solicitudes_mantenimiento WHERE articulo_id = $articulo_id AND estatus IN (0,1) LIMIT 1");
                                    if ($row = mysqli_fetch_assoc($solicitud)):
                                    ?>
                                        <form method="POST" action="../actions/cancelarSolicitud.php" style="display:inline;">
                                            <input type="hidden" name="solicitud_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-cancelar" onclick="return confirm('¿Estás seguro de cancelar esta solicitud?');">
                                                <i class="fas fa-times-circle"></i> Cancelar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn-solicitar" onclick="abrirModal(<?= $art['id'] ?>)">
                                            <i class="fas fa-wrench"></i> Solicitar mantenimiento
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                           
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <div class="logout-container">
                                <form action="../actions/cerrarSesionUsuario.php" method="POST">
                                    <button type="submit" class="btn-cerrar-sesion"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
                                </form>
                            </div>
        <?php endif; ?>
    </main>

    <!-- Modal para registrar solicitud -->
    <!-- Modal de Solicitud -->
    <div class="modal-solicitud-overlay" id="modalSolicitud">
        <div class="modal-solicitud-container">
            <span class="modal-solicitud-close" onclick="cerrarModalSolicitud()"><i class="fas fa-times"></i></span>
            <h2 class="modal-solicitud-title"><i class="fas fa-wrench"></i> Solicitar Mantenimiento</h2>
            <form method="POST" action="../actions/registrarSolicitud.php?area_id=<?= $area_id ?>">
                <input type="hidden" name="articulo_id" id="modalArticuloId">
                <div class="modal-solicitud-flex">
                    <div class="modal-solicitud-field">
                        <label class="modal-solicitud-label">Nombre del solicitante</label>
                        <input type="text" name="nombre_solicitante" class="modal-solicitud-input" required>
                    </div>
                    <div class="modal-solicitud-field">
                        <label class="modal-solicitud-label">Descripción del problema</label>
                        <textarea name="descripcion_problema" class="modal-solicitud-textarea" required></textarea>
                    </div>
                </div>
                <div class="modal-solicitud-buttons">
                    <button type="submit" class="btn-solicitud-guardar"><i class="fas fa-save"></i> Enviar</button>
                    <button type="button" class="btn-solicitud-cancelar" onclick="cerrarModalSolicitud()"><i class="fas fa-times-circle"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        //Script para cancelar solicitud
        function cancelarSolicitud(solicitudId, fila) {
            if (!confirm('¿Estás seguro de cancelar esta solicitud?')) return;

            fetch('../actions/cancelarSolicitud.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'solicitud_id=' + solicitudId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Eliminar la fila o actualizar columna con botón de solicitud
                        const filaElemento = document.getElementById('fila-' + fila);
                        if (filaElemento) {
                            filaElemento.querySelector('.acciones').innerHTML = `
                    <button class="btn-solicitar" onclick="abrirModal(${fila})">
                        <i class="fas fa-plus-circle"></i> Solicitar Mantenimiento
                    </button>`;
                        }
                    } else {
                        alert('Error al cancelar la solicitud.');
                    }
                });
        }
    </script>





    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        function abrirModal(id) {
            document.getElementById('modalArticuloId').value = id;
            document.getElementById('modalSolicitud').style.display = 'flex';
        }

        function cerrarModalSolicitud() {
            document.getElementById('modalSolicitud').style.display = 'none';
        }

        // Buscador con resaltado
        const input = document.getElementById('searchInput');
        if (input) {
            input.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tablaArticulos tbody tr');
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