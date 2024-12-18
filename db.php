<?php
$host = "localhost"; // Cambia esto si usas un servidor remoto
$username = "root";  // Usuario de MySQL (por defecto es 'root' en XAMPP)
$password = "";      // Contraseña de MySQL (por defecto es vacío en XAMPP)
$dbname = "test"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
