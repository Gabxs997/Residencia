<?php
session_start();

$usuario = $_POST["username"];
$contrasena = $_POST["password"];

require "../config/conexion.php";

// 1. Verificar si es administrador
$adminQuery = "SELECT * FROM administrador WHERE Usuario = '$usuario' AND Contrasena = '$contrasena'";
$adminResult = mysqli_query($conectar, $adminQuery);

if (mysqli_num_rows($adminResult) > 0) {
    $_SESSION['autentificado'] = true;
    $_SESSION['rol'] = 'admin';
    header("Location: ../Panel/administrador.php");
    exit;
}

// 2. Verificar si es técnico
$tecnicoQuery = "SELECT * FROM usuarios_tecnicos WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
$tecnicoResult = mysqli_query($conectar, $tecnicoQuery);

if (mysqli_num_rows($tecnicoResult) > 0) {
    $_SESSION['autentificado'] = true;
    $_SESSION['rol'] = 'tecnico';
    header("Location: ../Panel/tecnico.php"); // Asegúrate de tener esta página
    exit;
}

// 3. Si no coincide con ningún usuario
echo "
<script>
alert('ERROR EN LA AUTENTICACIÓN');
location.href = '../index.php?errorusuario=SI';
</script>
";

mysqli_close($conectar);
?>
