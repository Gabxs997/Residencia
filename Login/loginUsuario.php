<?php
require_once __DIR__ . '/../config/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conectar, $_POST['usuario']);
    $password = $_POST['password'];

    $query = mysqli_query($conectar, "SELECT * FROM usuarios_departamento WHERE usuario = '$usuario' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['contrasena'])) {
        $_SESSION['area_id'] = $user['area_id'];
        $_SESSION['usuario'] = $user['usuario'];
        header("Location: ../Panel/reportesT/solicitarMantenimiento.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Técnico</title>
    <link rel="stylesheet" href="../CSS/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 350px;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #5c1434;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #5c1434;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-login:hover {
            background-color: #7c1a4a;
        }
        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Acceso Usuarios por Área</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>