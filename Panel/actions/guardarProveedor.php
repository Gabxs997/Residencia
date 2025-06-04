<?php
require_once __DIR__ . '/../../config/conexion.php';

if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.history.back();</script>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.history.back();</script>');
}

// Sanitizar datos del proveedor (solo estos campos son obligatorios)
$camposObligatorios = ['razonSocial', 'direccion', 'telefono1'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.history.back();</script>');
    }
}

$razonSocial = mysqli_real_escape_string($conectar, $_POST['razonSocial']);
$direccion = mysqli_real_escape_string($conectar, $_POST['direccion']);
$telefono1 = mysqli_real_escape_string($conectar, $_POST['telefono1']);
$telefono2 = !empty($_POST['telefono2']) ? mysqli_real_escape_string($conectar, $_POST['telefono2']) : NULL;
$email = !empty($_POST['email']) ? mysqli_real_escape_string($conectar, $_POST['email']) : NULL;
$estado = !empty($_POST['estado']) ? mysqli_real_escape_string($conectar, $_POST['estado']) : 'Activo';

// Sanitizar datos del contacto (todos opcionales)
$nombreContacto = !empty($_POST['nombreContacto']) ? mysqli_real_escape_string($conectar, $_POST['nombreContacto']) : NULL;
$direccionContacto = !empty($_POST['direccionContacto']) ? mysqli_real_escape_string($conectar, $_POST['direccionContacto']) : NULL;
$telefonoContacto1 = !empty($_POST['telefonoContacto1']) ? mysqli_real_escape_string($conectar, $_POST['telefonoContacto1']) : NULL;
$telefonoContacto2 = !empty($_POST['telefonoContacto2']) ? mysqli_real_escape_string($conectar, $_POST['telefonoContacto2']) : NULL;
$emailContacto = !empty($_POST['emailContacto']) ? mysqli_real_escape_string($conectar, $_POST['emailContacto']) : NULL;
$estadoContacto = !empty($_POST['estadoContacto']) ? mysqli_real_escape_string($conectar, $_POST['estadoContacto']) : NULL;

mysqli_begin_transaction($conectar);

try {
    // Insertar proveedor
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
    
    // Insertar contacto solo si hay al menos un dato
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
    
    echo '
    <script>
    alert("PROVEEDOR GUARDADO CORRECTAMENTE");
    window.location.href = "../forms/altaProveedor.php";
    </script>
    ';
    
} catch (Exception $e) {
    mysqli_rollback($conectar);
    
    echo '
    <script>
    alert("ERROR: '.str_replace("'", "\\'", $e->getMessage()).'");
    window.history.back();
    </script>
    ';
}

mysqli_close($conectar);
?>