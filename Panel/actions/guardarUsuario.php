<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conectar, $_POST['usuario']);
    $contrasena = mysqli_real_escape_string($conectar, $_POST['password']);
    $area_id = intval($_POST['area_id']);

    // Encriptar la contraseña con password_hash
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Verificar si el nombre de usuario ya existe
    $verificar = mysqli_query($conectar, "SELECT id FROM usuarios_departamento WHERE usuario = '$usuario'");
    if (mysqli_num_rows($verificar) > 0) {
        echo "<script>alert('El nombre de usuario ya existe.'); window.location.href = 'crearUsuario.php';</script>";
        exit;
    }

    // Insertar el nuevo usuario
    $insert = mysqli_query($conectar, "
        INSERT INTO usuarios_departamento (usuario, contrasena, area_id)
        VALUES ('$usuario', '$hash', $area_id)
    ");

    if ($insert) {
        echo "<script>alert('Usuario creado exitosamente.'); window.location.href = '../crearUsuario.php';</script>";
    } else {
        echo "<script>alert('Error al crear el usuario.'); window.location.href = '../crearUsuario.php';</script>";
    }
}
?>
