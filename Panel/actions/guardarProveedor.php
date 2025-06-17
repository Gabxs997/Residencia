<?php
require_once __DIR__ . '/../../config/conexion.php';

function limpiarNombreProveedor($nombre) {
    $nombre = strtoupper($nombre);
    $nombre = str_replace(
        ['.', ',', 'S.A. DE C.V.', 'SA DE CV', 'S.A CV', 'S.A. CV', ' S.A. ', ' DE C.V. '],
        ['','','SADECV','SADECV','SADECV','SADECV','SADECV','SADECV'],
        $nombre
    );
    return trim(preg_replace('/\s+/', ' ', $nombre));
}

if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.history.back();</script>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.history.back();</script>');
}

$camposObligatorios = ['razonSocial', 'direccion', 'telefono1'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.history.back();</script>');
    }
}

$razonSocial = strtoupper(trim(mysqli_real_escape_string($conectar, $_POST['razonSocial'])));
$direccion = mysqli_real_escape_string($conectar, $_POST['direccion']);
$telefono1 = mysqli_real_escape_string($conectar, $_POST['telefono1']);
$telefono2 = !empty($_POST['telefono2']) ? mysqli_real_escape_string($conectar, $_POST['telefono2']) : NULL;
$email = !empty($_POST['email']) ? mysqli_real_escape_string($conectar, $_POST['email']) : NULL;
$estado = !empty($_POST['estado']) ? mysqli_real_escape_string($conectar, $_POST['estado']) : 'Activo';

$nombreContacto = !empty($_POST['nombreContacto']) ? mysqli_real_escape_string($conectar, $_POST['nombreContacto']) : NULL;
$direccionContacto = !empty($_POST['direccionContacto']) ? mysqli_real_escape_string($conectar, $_POST['direccionContacto']) : NULL;
$telefonoContacto1 = !empty($_POST['telefonoContacto1']) ? mysqli_real_escape_string($conectar, $_POST['telefonoContacto1']) : NULL;
$telefonoContacto2 = !empty($_POST['telefonoContacto2']) ? mysqli_real_escape_string($conectar, $_POST['telefonoContacto2']) : NULL;
$emailContacto = !empty($_POST['emailContacto']) ? mysqli_real_escape_string($conectar, $_POST['emailContacto']) : NULL;
$estadoContacto = !empty($_POST['estadoContacto']) ? mysqli_real_escape_string($conectar, $_POST['estadoContacto']) : NULL;

$nombreLimpio = limpiarNombreProveedor($razonSocial);
$verificar = mysqli_query($conectar, "SELECT id, razon_social, eliminado FROM proveedores");

while ($row = mysqli_fetch_assoc($verificar)) {
    $comparar = limpiarNombreProveedor($row['razon_social']);
    similar_text($comparar, $nombreLimpio, $porcentaje);
    
    if ($porcentaje >= 85) {
        $id = $row['id'];

        if ($row['eliminado'] == 1) {
            // Reactivar proveedor
            $actualizar = mysqli_query($conectar, "
                UPDATE proveedores SET
                    razon_social = '$razonSocial',
                    direccion = '$direccion',
                    telefono1 = '$telefono1',
                    telefono2 = " . ($telefono2 ? "'$telefono2'" : "NULL") . ",
                    email = " . ($email ? "'$email'" : "NULL") . ",
                    estado = '$estado',
                    eliminado = 0
                WHERE id = $id
            ");
            if ($actualizar) {
                echo "<script>alert('Se reactivó un proveedor similar: $razonSocial'); window.location.href = '../tablas/tablaProveedores.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error al reactivar proveedor.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Ya existe un proveedor similar: {$row['razon_social']}'); window.history.back();</script>";
            exit;
        }
    }
}

mysqli_begin_transaction($conectar);

try {
    $sqlProveedor = "INSERT INTO proveedores (
        razon_social, direccion, telefono1, telefono2, email, estado
    ) VALUES (
        '$razonSocial', '$direccion', '$telefono1', " . 
        ($telefono2 ? "'$telefono2'" : "NULL") . ", " .
        ($email ? "'$email'" : "NULL") . ", '$estado'
    )";

    if (!mysqli_query($conectar, $sqlProveedor)) {
        throw new Exception("Error al guardar proveedor: " . mysqli_error($conectar));
    }

    $proveedorId = mysqli_insert_id($conectar);

    if ($nombreContacto || $telefonoContacto1 || $emailContacto) {
        $sqlContacto = "INSERT INTO contactos_proveedores (
            proveedor_id, nombre, direccion, telefono1, telefono2, email, estado
        ) VALUES (
            '$proveedorId', " .
            ($nombreContacto ? "'$nombreContacto'" : "NULL") . ", " .
            ($direccionContacto ? "'$direccionContacto'" : "NULL") . ", " .
            ($telefonoContacto1 ? "'$telefonoContacto1'" : "NULL") . ", " .
            ($telefonoContacto2 ? "'$telefonoContacto2'" : "NULL") . ", " .
            ($emailContacto ? "'$emailContacto'" : "NULL") . ", " .
            ($estadoContacto ? "'$estadoContacto'" : "NULL") . "
        )";

        if (!mysqli_query($conectar, $sqlContacto)) {
            throw new Exception("Error al guardar contacto: " . mysqli_error($conectar));
        }
    }

    mysqli_commit($conectar);

    echo '<script>
        alert("PROVEEDOR GUARDADO CORRECTAMENTE");
        window.location.href = "../tablas/tablaProveedores.php";
    </script>';

} catch (Exception $e) {
    mysqli_rollback($conectar);
    echo '<script>alert("ERROR: '.str_replace("'", "\\'", $e->getMessage()).'"); window.history.back();</script>';
}

mysqli_close($conectar);
?>
