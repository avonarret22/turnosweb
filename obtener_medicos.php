<?php
include('db.php'); // Conexión a la base de datos

// Verificar si se recibe el ID de especialidad
if (isset($_POST['id_especialidad']) && !empty($_POST['id_especialidad'])) {
    $id_especialidad = $_POST['id_especialidad'];

    // Depuración
    error_log("ID Especialidad recibido: " . $id_especialidad);

    // Consulta SQL para obtener médicos
    $query = "SELECT id_medico, CONCAT(nombre, ' ', apellido) AS nombre_completo 
              FROM medico 
              WHERE id_especialidad = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_especialidad);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generar opciones para el select
    if ($result->num_rows > 0) {
        while ($medico = $result->fetch_assoc()) {
            echo "<option value='" . $medico['id_medico'] . "'>" . $medico['nombre_completo'] . "</option>";
        }
    } else {
        echo "<option value=''>No hay médicos disponibles</option>";
    }
} else {
    echo "<option value=''>Error en la solicitud</option>";
}
?>
