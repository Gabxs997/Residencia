<?php
require_once __DIR__ . '/../../config/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM partidas WHERE id = $id LIMIT 1";
$result = mysqli_query($conectar, $query);
$partida = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_partida = mysqli_real_escape_string($conectar, $_POST['numero_partida']);
    $nombre_partida = mysqli_real_escape_string($conectar, $_POST['nombre_partida']);
    $numero_subpartida = mysqli_real_escape_string($conectar, $_POST['numero_subpartida']);
    $nombre_subpartida = mysqli_real_escape_string($conectar, $_POST['nombre_subpartida']);

    $updateQuery = "UPDATE partidas SET
        numero_partida = '$numero_partida',
        nombre_partida = '$nombre_partida',
        numero_subpartida = '$numero_subpartida',
        nombre_subpartida = '$nombre_subpartida'
        WHERE id = $id";

    if (mysqli_query($conectar, $updateQuery)) {
        echo "<script>
            alert('Partida actualizada correctamente');
            window.location.href = '../tablas/tablaPartidas.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Error al actualizar la partida');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Partida</title>
    <link rel="stylesheet" href="../../CSS/modal.css">
    <link rel="stylesheet" href="../../CSS/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="modal-overlay">
        <div class="modal-container">
            <span class="modal-close" onclick="window.location.href='../tablas/tablaPartidas.php'">
                <i class="fas fa-times"></i>
            </span>

            <h2 class="modal-title">Editar Partida ID: <?= $partida['id'] ?></h2>

            <form method="POST" action="">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="numero_partida">Partida Presupuestal</label>
                        <input type="text" name="numero_partida" id="numero_partida" value="<?= htmlspecialchars($partida['numero_partida']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nombre_partida">Nombre Partida</label>
                        <input type="text" name="nombre_partida" id="nombre_partida" value="<?= htmlspecialchars($partida['nombre_partida']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="numero_subpartida">Partida Contable</label>
                        <input type="text" name="numero_subpartida" id="numero_subpartida" value="<?= htmlspecialchars($partida['numero_subpartida']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nombre_subpartida">Nombre Subpartida</label>
                        <input type="text" name="nombre_subpartida" id="nombre_subpartida" value="<?= htmlspecialchars($partida['nombre_subpartida']) ?>" required>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="window.location.href='../tablas/tablaPartidas.php'">Cancelar</button>
                    <button type="submit" class="btn btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
