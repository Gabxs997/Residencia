<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="font/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <section>
        <div class="login-container">
            <h2>Inicio sesión</h2>
            <form action="Login/autenticar.php" method="POST">
                <label for="username"> <i class="fas fa-user"></i>Usuario:</label>
                <input type="text" id="username" name="username" required placeholder="Ingresa tu usuario">
                <label for="password"> <i class="fas fa-lock"></i>Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                <button type="submit">Iniciar sesión</button> 
            </form>
        </div>
    </section>
</body>
</html> 