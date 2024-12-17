<?php
// Iniciar sesión
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de login
header('Location: http://localhost/panaderia/views/login.php');
exit();
?>
