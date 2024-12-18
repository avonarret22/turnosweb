<!-- /config.php -->

<?php
$host = 'localhost'; // o tu servidor
$dbname = 'turnos_medicos'; // Nombre de la base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña del usuario

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error al conectar a la base de datos: ' . $e->getMessage();
}
?>
