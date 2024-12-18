<?php
session_start();
include('db.php'); // Conexión a la base de datos

include('includes/header.php'); // Header reutilizable

// Variables
$mensaje = "";
$correo_paciente = isset($_POST['correo']) ? trim($_POST['correo']) : "";
$resultados = [];

// Procesar la consulta de turnos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($correo_paciente)) {
    $query = "SELECT t.fecha, t.hora, m.nombre AS medico, e.nombre AS especialidad
              FROM turno t
              LEFT JOIN medico m ON t.id_medico = m.id_medico
              LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad
              LEFT JOIN paciente p ON t.id_paciente = p.id_paciente
              WHERE p.correo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $resultados = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $mensaje = "No se encontraron turnos para el correo ingresado.";
    }
}
?>

<section class="container">
    <h2>Gestión de Turnos</h2>

    <!-- Botones de navegación -->
    <div>
        <a href="turnos.php?accion=consultar" class="btn">Consultar Turnos</a>
        <a href="agregar_turno.php" class="btn">Solicitar Turno</a>
    </div>

    <!-- Sección de Consulta de Turnos -->
    <?php if (isset($_GET['accion']) && $_GET['accion'] === 'consultar'): ?>
        <h3>Consultar Turnos</h3>
        <form method="POST" action="turnos.php?accion=consultar">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" placeholder="Ingrese su correo" required>
            <input type="submit" value="Buscar" class="btn">
        </form>

        <?php if (!empty($mensaje)): ?>
            <p><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (!empty($resultados)): ?>
            <h4>Turnos Agendados</h4>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $turno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($turno['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($turno['hora']); ?></td>
                            <td><?php echo htmlspecialchars($turno['medico']); ?></td>
                            <td><?php echo htmlspecialchars($turno['especialidad']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include('includes/footer.php'); ?>
