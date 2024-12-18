<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre'];

// Procesar la cancelación del turno
if (isset($_GET['elimin_turno']) && !empty($_GET['elimin_turno'])) {
    $id_turno = $_GET['elimin_turno'];
    $id_estado_cancelado = 5; // ID del estado 'Cancelado'

    // Actualizar el estado del turno a 'Cancelado'
    $query_cancelar = "UPDATE turno SET id_estado_turno = ? WHERE id_turno = ?";
    $stmt = $conn->prepare($query_cancelar);
    $stmt->bind_param("ii", $id_estado_cancelado, $id_turno);

    if ($stmt->execute()) {
        echo "<script>alert('El turno ha sido cancelado exitosamente.'); window.location.href = 'panel_usuario.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al cancelar el turno.'); window.location.href = 'panel_usuario.php';</script>";
        exit();
    }
}


// Obtener la información del usuario y del paciente relacionado
$query_user = "SELECT u.*, p.id_paciente, p.telefono, p.calle, p.numero, p.ciudad, 
                      p.provincia, p.codigo_postal, o.nombre AS obra_social 
               FROM usuarios u 
               LEFT JOIN paciente p ON u.email = p.correo
               LEFT JOIN obra_social o ON p.id_obra_social = o.id_obra_social
               WHERE u.id_usuario = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$user_result = $stmt->get_result();
$usuario = $user_result->fetch_assoc();

// Obtener los turnos del usuario
$query_turnos = "SELECT t.id_turno, t.fecha, t.hora, e.nombre AS especialidad, 
                        CONCAT(m.nombre, ' ', m.apellido) AS medico, te.nombre AS estado 
                 FROM turno t
                 LEFT JOIN paciente p ON t.id_paciente = p.id_paciente
                 LEFT JOIN medico m ON t.id_medico = m.id_medico
                 LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad
                 LEFT JOIN turno_estado te ON t.id_estado_turno = te.id_estado_turno
                 WHERE p.correo = ?";
$stmt = $conn->prepare($query_turnos);
$stmt->bind_param("s", $usuario['email']);
$stmt->execute();
$turnos = $stmt->get_result();


// Obtener historial médico y recetas
$query_historial = "SELECT hc.descripcion AS historia_clinica, r.medicamento, r.dosis 
                    FROM historia_clinica hc
                    LEFT JOIN receta r ON hc.id_paciente = r.id_consulta
                    WHERE hc.id_paciente = ?";
$stmt = $conn->prepare($query_historial);
$stmt->bind_param("i", $usuario['id_paciente']);
$stmt->execute();
$historial = $stmt->get_result();

include('includes/header.php');
?>

<section class="container" style="margin-top: 20px;">
    <h2 style="text-align: center;">Panel de Usuario</h2>
    <div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 10px;">
        <h3>Información Personal</h3>
        <p><strong>Nombre:</strong> <?php echo $usuario['nombre'] . " " . $usuario['apellido']; ?></p>
        <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
        <p><strong>Teléfono:</strong> <?php echo $usuario['telefono'] ?? "No registrado"; ?></p>
        <p><strong>Obra Social:</strong> <?php echo $usuario['obra_social'] ?? "No registrada"; ?></p>
        <p><strong>Dirección:</strong> 
            <?php 
            echo ($usuario['calle'] ?? '') . " " . ($usuario['numero'] ?? '') . ", " . 
                 ($usuario['ciudad'] ?? '') . ", " . ($usuario['provincia'] ?? '') . " (" . 
                 ($usuario['codigo_postal'] ?? '') . ")"; 
            ?>
        </p>
        <a href="modificar_usuario.php" class="btn" style="display: inline-block; margin-top: 10px;">Modificar Información</a>
    </div>

    <!-- Turnos del Usuario -->
    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3>Mis Turnos</h3>
        <?php if ($turnos->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Especialidad</th>
                        <th>Médico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($turno = $turnos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $turno['fecha']; ?></td>
                            <td><?php echo $turno['hora']; ?></td>
                            <td><?php echo $turno['especialidad']; ?></td>
                            <td><?php echo $turno['medico']; ?></td>
                            <td><?php echo $turno['estado']; ?></td>
                            <td>
                                <?php if ($turno['fecha'] >= date('Y-m-d') && $turno['estado'] !== 'Cancelado'): ?>
                                    <a href="panel_usuario.php?elimin_turno=<?php echo $turno['id_turno']; ?>"
                                       onclick="return confirm('¿Seguro que deseas cancelar este turno?');" 
                                       style="color: red; text-decoration: none; font-weight: bold;">
                                       Cancelar</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes turnos solicitados.</p>
        <?php endif; ?>
    </div>

    <!-- Solicitar Turno -->
    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 10px;">
        <h3>Solicitar Nuevo Turno</h3>
        <form action="agregar_turno.php" method="POST">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" required>

            <label for="hora">Hora:</label>
            <input type="time" name="hora" required>

            <label for="obra_social">Obra Social:</label>
            <select name="obra_social" required>
                <option value="">Seleccione su obra social</option>
                <?php
                $obras = $conn->query("SELECT * FROM obra_social");
                while ($obra = $obras->fetch_assoc()):
                ?>
                    <option value="<?php echo $obra['id_obra_social']; ?>"><?php echo $obra['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="especialidad">Especialidad:</label>
            <select name="especialidad" required>
                <option value="">Seleccione una especialidad</option>
                <?php
                $especialidades = $conn->query("SELECT * FROM especialidad");
                while ($esp = $especialidades->fetch_assoc()):
                ?>
                    <option value="<?php echo $esp['id_especialidad']; ?>"><?php echo $esp['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <br><br>
            <input type="submit" value="Solicitar Turno" class="btn">
        </form>
    </div>

    <!-- Historial Médico -->
    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 10px; margin-top: 20px;">
        <h3>Historial Médico y Recetas</h3>
        <?php if ($historial->num_rows > 0): ?>
            <ul>
                <?php while ($item = $historial->fetch_assoc()): ?>
                    <li><?php echo "Historia Clínica: " . $item['historia_clinica'] . " | Medicamento: " . $item['medicamento'] . " | Dosis: " . $item['dosis']; ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay historial médico registrado.</p>
        <?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>
