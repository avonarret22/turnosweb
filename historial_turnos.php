<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "Acceso denegado. Debes iniciar sesión para acceder a esta página.";
    exit();
}

include('includes/header.php'); // Header reutilizable

// Variables de filtro
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_paciente = isset($_GET['paciente']) ? $_GET['paciente'] : '';
$filtro_medico = isset($_GET['medico']) ? $_GET['medico'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Consulta base
$query = "SELECT t.id_turno, t.fecha, t.hora, p.nombre AS paciente, m.nombre AS medico, e.nombre AS estado
          FROM turno t
          LEFT JOIN paciente p ON t.id_paciente = p.id_paciente
          LEFT JOIN medico m ON t.id_medico = m.id_medico
          LEFT JOIN turno_estado e ON t.id_estado_turno = e.id_estado_turno
          WHERE 1=1";

// Aplicar filtros dinámicamente
$params = [];
$types = "";

if ($filtro_fecha) {
    $query .= " AND t.fecha = ?";
    $params[] = $filtro_fecha;
    $types .= "s";
}
if ($filtro_paciente) {
    $query .= " AND p.nombre LIKE ?";
    $params[] = "%" . $filtro_paciente . "%";
    $types .= "s";
}
if ($filtro_medico) {
    $query .= " AND m.nombre LIKE ?";
    $params[] = "%" . $filtro_medico . "%";
    $types .= "s";
}
if ($filtro_estado) {
    $query .= " AND e.nombre LIKE ?";
    $params[] = "%" . $filtro_estado . "%";
    $types .= "s";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="container">
    <h2>Historial de Turnos</h2>

    <!-- Formulario de Filtros -->
    <form method="GET" action="historial_turnos.php">
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" value="<?php echo $filtro_fecha; ?>">

        <label for="paciente">Paciente:</label>
        <input type="text" name="paciente" placeholder="Nombre del paciente" value="<?php echo $filtro_paciente; ?>">

        <label for="medico">Médico:</label>
        <input type="text" name="medico" placeholder="Nombre del médico" value="<?php echo $filtro_medico; ?>">

        <label for="estado">Estado:</label>
        <input type="text" name="estado" placeholder="Estado del turno" value="<?php echo $filtro_estado; ?>">

        <input type="submit" value="Filtrar" class="btn">
        <a href="historial_turnos.php" class="btn">Limpiar Filtros</a>
    </form>

    <!-- Tabla de Turnos -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_turno']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['hora']; ?></td>
                        <td><?php echo $row['paciente']; ?></td>
                        <td><?php echo $row['medico']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No se encontraron turnos con los filtros aplicados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include('includes/footer.php'); ?>
