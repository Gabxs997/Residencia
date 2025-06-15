<?php
session_start();
require "../config/conexion.php";

$usuario = trim($_POST["username"]);
$contrasena = $_POST["password"];

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
    header("Location: ../Panel/tecnico.php");
    exit;
}

// 3. Verificar si es usuario por área (usuarios_departamento)
$userQuery = mysqli_query($conectar, "SELECT * FROM usuarios_departamento WHERE usuario = '$usuario' LIMIT 1");
$user = mysqli_fetch_assoc($userQuery);

if ($user && password_verify($contrasena, $user['contrasena'])) {
    $_SESSION['area_id'] = $user['area_id'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['rol'] = 'usuario';
    header("Location: ../Panel/departamentos.php");
    exit;
}

// 4. Fallo en autenticación
echo "
<script>
    alert('ERROR EN LA AUTENTICACIÓN');
    location.href = '../index.php?errorusuario=SI';
</script>
";

mysqli_close($conectar);
