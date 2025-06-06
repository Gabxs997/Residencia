<?php
session_start();

// ...

// Cerrar sesión
$_SESSION = array(); // Limpiar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir a la página de inicio de sesión u otra página
echo '<script>
alert("CERRASTE SESION CORRECTAMENTE");
location.href = "../reportesT.php";
</script>
';
exit();
?>