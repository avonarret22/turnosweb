<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado. Solo administradores pueden editar turnos.";
    exit();
}

// Obtener ID del turno
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID de turno inválido.";
    exit();
}

$id_turno = $_GET['id'];

// Obtener datos del turno existente
$query = "SELECT * FROM turno WHERE id_turno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_turno);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();

if (!$turno) {
    echo "Turno no encontrado.";
    exit();
}

// Obtener listas para el formulario
$pacientes = $conn->query("SELECT id_paciente, nombre FROM paciente GROUP BY nombre");
$especialidades = $conn->query("SELECT id_especialidad, nombre FROM especialidad GROUP BY nombre");
$estados = [
    1 => 'Pendiente',
    2 => 'En curso',
    3 => 'Completado',
    4 => 'Reprogramado',
    5 => 'Cancelado'
];

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_paciente = $_POST['id_paciente'];
    $id_estado_turno = $_POST['id_estado_turno'];

    if ($fecha && $hora && $id_paciente && $id_estado_turno) {
        $update_query = "UPDATE turno SET fecha = ?, hora = ?, id_paciente = ?, id_estado_turno = ? WHERE id_turno = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssiii", $fecha, $hora, $id_paciente, $id_estado_turno, $id_turno);

        if ($stmt->execute()) {
            echo "<script>alert('Turno actualizado correctamente.'); window.location.href = 'turnos.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al actualizar el turno.');</script>";
        }
    } else {
        echo "<script>alert('Todos los campos son obligatorios.');</script>";
    }
}

include('includes/header.php');
?>

<section class="container">
    <h2>Editar Turno</h2>
    <form action="" method="POST">
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" value="<?php echo $turno['fecha']; ?>" required>

        <label for="hora">Hora:</label>
        <input type="time" name="hora" value="<?php echo $turno['hora']; ?>" required>

        <label for="id_paciente">Paciente:</label>
        <select name="id_paciente" required>
            <?php while ($paciente = $pacientes->fetch_assoc()): ?>
                <option value="<?php echo $paciente['id_paciente']; ?>" 
                    <?php echo ($paciente['id_paciente'] == $turno['id_paciente']) ? 'selected' : ''; ?>>
                    <?php echo $paciente['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="id_estado_turno">Estado:</label>
        <select name="id_estado_turno" required>
            <?php foreach ($estados as $key => $value): ?>
                <option value="<?php echo $key; ?>" 
                    <?php echo ($key == $turno['id_estado_turno']) ? 'selected' : ''; ?>>
                    <?php echo $value; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <input type="submit" value="Actualizar Turno" class="btn">
        <a href="turnos.php" class="btn">Cancelar</a>
    </form>
</section>

<?php include('includes/footer.php'); ?>
