<?php
// 1. Incluir archivo de conexión
require_once __DIR__ . '/../../config/conexion.php';

// Verificar conexión
if (!$conectar) {
    die('<script>alert("Error de conexión a la base de datos"); window.history.back();</script>');
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('<script>alert("El formulario debe enviarse por POST"); window.history.back();</script>');
}

// 3. Lista de campos obligatorios
$camposObligatorios = [
    'ur', 'noInventario', 'cabm', 'descripcion', 'descripcionDetalle',
    'partidaPresupuestal', 'partidaContable', 'fechaAlta', 'fechaDocumento',
    'tipoBien', 'noContrato', 'noFactura', 'proveedor_id', 'estadoBien',
    'ubicacion', 'rfc', 'importe', 'origen'
];

// 4. Validar campos obligatorios
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        die('<script>alert("El campo '.$campo.' es obligatorio"); window.history.back();</script>');
    }
}

// 5. Sanitizar y preparar datos
$ur = mysqli_real_escape_string($conectar, $_POST['ur']);
$noInventario = mysqli_real_escape_string($conectar, $_POST['noInventario']);
$cabm = mysqli_real_escape_string($conectar, $_POST['cabm']);
$descripcion = mysqli_real_escape_string($conectar, $_POST['descripcion']);
$descripcionDetalle = mysqli_real_escape_string($conectar, $_POST['descripcionDetalle']);
$partidaPresupuestal = mysqli_real_escape_string($conectar, $_POST['partidaPresupuestal']);
$partidaContable = mysqli_real_escape_string($conectar, $_POST['partidaContable']);
$fechaAlta = mysqli_real_escape_string($conectar, $_POST['fechaAlta']);
$fechaDocumento = mysqli_real_escape_string($conectar, $_POST['fechaDocumento']);
$tipoBien = mysqli_real_escape_string($conectar, $_POST['tipoBien']);
$noContrato = mysqli_real_escape_string($conectar, $_POST['noContrato']);
$noFactura = mysqli_real_escape_string($conectar, $_POST['noFactura']);
$proveedor_id = (int)$_POST['proveedor_id'];
$serie = isset($_POST['serie']) ? mysqli_real_escape_string($conectar, $_POST['serie']) : '';
$modelo = isset($_POST['modelo']) ? mysqli_real_escape_string($conectar, $_POST['modelo']) : '';
$marca = isset($_POST['marca']) ? mysqli_real_escape_string($conectar, $_POST['marca']) : '';
$estadoBien = mysqli_real_escape_string($conectar, $_POST['estadoBien']);
$ubicacion = mysqli_real_escape_string($conectar, $_POST['ubicacion']);
$rfc = mysqli_real_escape_string($conectar, $_POST['rfc']);
$importe = (float)$_POST['importe'];
$observaciones = isset($_POST['observaciones']) ? mysqli_real_escape_string($conectar, $_POST['observaciones']) : '';
$origen = mysqli_real_escape_string($conectar, $_POST['origen']);

// 5.1 Verificar si ya existe el número de inventario
$checkInventario = mysqli_query($conectar, "SELECT id FROM articulos WHERE LOWER(no_inventario) = LOWER('$noInventario')");

if (mysqli_num_rows($checkInventario) > 0) {
    echo '
    <script>
    alert("El número de inventario ya existe en la base de datos.");
    window.history.back();
    </script>
    ';
    exit;
}


// 6. Preparar la consulta SQL
$sql = "INSERT INTO articulos (
    ur, no_inventario, cabm, descripcion, descripcion_detalle,
    partida_presupuestal, partida_contable, fecha_alta, fecha_documento,
    tipo_bien, no_contrato, no_factura, proveedor_id, serie, modelo, marca,
    estado_bien, ubicacion, rfc_responsable, importe, observaciones, origen
) VALUES (
    '$ur', '$noInventario', '$cabm', '$descripcion', '$descripcionDetalle',
    '$partidaPresupuestal', '$partidaContable', '$fechaAlta', '$fechaDocumento',
    '$tipoBien', '$noContrato', '$noFactura', $proveedor_id, '$serie', '$modelo', '$marca',
    '$estadoBien', '$ubicacion', '$rfc', $importe, '$observaciones', '$origen'
)";

// 7. Ejecutar consulta y mostrar resultado
if (mysqli_query($conectar, $sql)) {
    echo '
    <script>
    alert("SE GUARDARON CORRECTAMENTE LOS DATOS DEL ARTÍCULO");
    window.location.href = "../forms/altaArticulo.php";
    </script>
    ';
} else {
    echo '
    <script>
    alert("ERROR AL GUARDAR: '.str_replace("'", "\\'", mysqli_error($conectar)).'");
    window.history.back();
    </script>
    ';
}

// 8. Cerrar conexión
mysqli_close($conectar);
?>
