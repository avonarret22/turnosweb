<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Acceso denegado. Debes iniciar sesión.'); window.location.href = 'login.php';</script>";
    exit();
}

// Verificar si se recibe el ID del turno
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID de turno inválido.'); window.location.href = 'panel_usuario.php';</script>";
    exit();
}

$id_turno = $_GET['id'];
$id_estado_cancelado = 5; // ID del estado 'Cancelado'

// Actualizar el estado del turno
$query = "UPDATE turno SET id_estado_turno = ? WHERE id_turno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_estado_cancelado, $id_turno);

if ($stmt->execute()) {
    echo "<script>alert('El turno ha sido cancelado exitosamente.'); window.location.href = 'panel_usuario.php';</script>";
} else {
    echo "<script>alert('Error al cancelar el turno.'); window.location.href = 'panel_usuario.php';</script>";
}

$stmt->close();
$conn->close();
?>
