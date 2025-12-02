<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destruir la sesión
session_destroy();

// Headers para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir al login
header("Location: login.php");
exit();
?>



