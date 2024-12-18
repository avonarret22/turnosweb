<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el id_usuario de la sesión
$id_usuario = $_SESSION['usuario_id'];

// Obtener el id_paciente relacionado con el usuario logueado
$query_paciente = "SELECT id_paciente FROM paciente WHERE id_usuario = ?";
$stmt_paciente = $conn->prepare($query_paciente);
$stmt_paciente->bind_param("i", $id_usuario);
$stmt_paciente->execute();
$result_paciente = $stmt_paciente->get_result();

if ($result_paciente->num_rows == 0) {
    echo "<script>alert('Error: No se encontró el paciente asociado al usuario.'); window.location.href = 'panel_usuario.php';</script>";
    exit();
}

$row_paciente = $result_paciente->fetch_assoc();
$id_paciente = $row_paciente['id_paciente'];

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_obra_social = $_POST['obra_social'];
    $id_especialidad = $_POST['especialidad'];
    $id_estado_turno = 1; // Estado por defecto: Pendiente

    // Validar si el médico está relacionado con la especialidad
    $query_medico = "SELECT id_medico FROM medico WHERE id_especialidad = ? LIMIT 1";
    $stmt_medico = $conn->prepare($query_medico);
    $stmt_medico->bind_param("i", $id_especialidad);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();

    if ($result_medico->num_rows > 0) {
        $row_medico = $result_medico->fetch_assoc();
        $id_medico = $row_medico['id_medico'];

        // Insertar el turno en la base de datos
        $query_insert = "INSERT INTO turno (fecha, hora, id_paciente, id_medico, id_estado_turno) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("ssiii", $fecha, $hora, $id_paciente, $id_medico, $id_estado_turno);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Turno registrado correctamente.'); window.location.href = 'panel_usuario.php';</script>";
        } else {
            echo "<script>alert('Error al registrar el turno.'); window.location.href = 'panel_usuario.php';</script>";
        }
    } else {
        echo "<script>alert('No hay médicos disponibles para la especialidad seleccionada.'); window.location.href = 'panel_usuario.php';</script>";
    }
}
?>


<section class="container">
    <h2 style="text-align: center; margin-bottom: 20px;">Solicitar Turno</h2>
    <form action="agregar_turno.php" method="POST" style="max-width: 600px; margin: 0 auto;" class="turno-form">
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required>

        <label for="hora">Hora:</label>
        <input type="time" name="hora" required>

        <label for="obra_social">Obra Social:</label>
        <select name="obra_social" required>
            <option value="">Seleccione su obra social</option>
            <?php while ($obra = $obras_sociales->fetch_assoc()): ?>
                <option value="<?php echo $obra['id_obra_social']; ?>">
                    <?php echo $obra['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="especialidad">Especialidad:</label>
        <select name="especialidad" required>
            <option value="">Seleccione una especialidad</option>
            <?php while ($esp = $especialidades->fetch_assoc()): ?>
                <option value="<?php echo $esp['id_especialidad']; ?>">
                    <?php echo $esp['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <br><br>
        <input type="submit" value="Solicitar Turno" class="btn">
        <a href="panel_usuario.php" class="btn">Volver</a>
    </form>
</section>

<?php include('includes/footer.php'); ?>
