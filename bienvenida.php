<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "Acceso denegado. Debes iniciar sesión para acceder a esta página.";
    exit();
}

echo "<h1>Bienvenido al Sitio</h1>";
echo "<p>Hola, " . $_SESSION['nombre'] . "! Has iniciado sesión correctamente.</p>";
?>
