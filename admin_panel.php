<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/header.php');

// Procesar formularios para agregar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar médico
    if (isset($_POST['agregar_medico'])) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $matricula = $_POST['matricula'];
        $id_especialidad = $_POST['id_especialidad'];

        $query = "INSERT INTO medico (nombre, apellido, matricula, id_especialidad) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $nombre, $apellido, $matricula, $id_especialidad);
        $stmt->execute();
    }

    // Agregar especialidad
    if (isset($_POST['agregar_especialidad'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        $query = "INSERT INTO especialidad (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nombre, $descripcion);
        $stmt->execute();
    }

    // Agregar obra social
    if (isset($_POST['agregar_obra_social'])) {
        $nombre = $_POST['nombre'];

        $query = "INSERT INTO obra_social (nombre) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
    }
}

// Consultar datos existentes
$medicos = $conn->query("SELECT m.*, e.nombre AS especialidad FROM medico m LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad");
$especialidades = $conn->query("SELECT * FROM especialidad");
$obras_sociales = $conn->query("SELECT * FROM obra_social");
$mensajes = $conn->query("SELECT * FROM datos ORDER BY fecha DESC");
?>

<section class="panel-container">
    <h2>Panel de Administrador</h2>

    <!-- Sección Agregar Médico -->
    <div>
        <h3>Agregar Médico</h3>
        <form method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
            <label>Apellido:</label>
            <input type="text" name="apellido" required>
            <label>Matrícula:</label>
            <input type="text" name="matricula" required>
            <label>Especialidad:</label>
            <select name="id_especialidad" required>
                <option value="">Seleccione una especialidad</option>
                <?php while ($esp = $especialidades->fetch_assoc()): ?>
                    <option value="<?php echo $esp['id_especialidad']; ?>">
                        <?php echo $esp['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="submit" name="agregar_medico" value="Agregar Médico" class="btn">
        </form>
    </div>

    <!-- Sección Agregar Especialidad -->
    <div>
        <h3>Agregar Especialidad</h3>
        <form method="POST">
            <label>Nombre de la Especialidad:</label>
            <input type="text" name="nombre" required>
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required></textarea>
            <input type="submit" name="agregar_especialidad" value="Agregar Especialidad" class="btn">
        </form>
    </div>

    <!-- Sección Agregar Obra Social -->
    <div>
        <h3>Agregar Obra Social</h3>
        <form method="POST">
            <label>Nombre de la Obra Social:</label>
            <input type="text" name="nombre" required>
            <input type="submit" name="agregar_obra_social" value="Agregar Obra Social" class="btn">
        </form>
    </div>

    <!-- Mostrar Médicos Registrados -->
    <div>
        <h3>Médicos Registrados</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Matrícula</th>
                <th>Especialidad</th>
            </tr>
            <?php while ($medico = $medicos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $medico['id_medico']; ?></td>
                    <td><?php echo $medico['nombre']; ?></td>
                    <td><?php echo $medico['apellido']; ?></td>
                    <td><?php echo $medico['matricula']; ?></td>
                    <td><?php echo $medico['especialidad']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Mostrar Obras Sociales -->
    <div>
        <h3>Obras Sociales Registradas</h3>
        <ul>
            <?php while ($obra = $obras_sociales->fetch_assoc()): ?>
                <li><?php echo $obra['nombre']; ?></li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Mostrar Mensajes de Contacto -->
    <div>
        <h3>Mensajes de Contacto</h3>
        <?php if ($mensajes->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Mensaje</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                </tr>
                <?php while ($mensaje = $mensajes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mensaje['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['email']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['motivo']); ?></td>
                        <td><?php echo $mensaje['fecha']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No hay mensajes de contacto.</p>
        <?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>
