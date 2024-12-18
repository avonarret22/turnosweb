<?php
session_start();
include('db.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Obtener la información actual del usuario y del paciente relacionado
$query_user = "SELECT u.*, p.id_paciente, p.telefono, p.calle, p.numero, p.ciudad, 
                      p.provincia, p.codigo_postal, p.id_obra_social 
               FROM usuarios u 
               LEFT JOIN paciente p ON u.email = p.correo
               WHERE u.id_usuario = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$user_result = $stmt->get_result();
$usuario = $user_result->fetch_assoc();

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $obra_social = $_POST['obra_social'];
    $calle = trim($_POST['calle']);
    $numero = trim($_POST['numero']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $codigo_postal = trim($_POST['codigo_postal']);

    // Actualizar la tabla usuarios
    $update_user = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($update_user);
    $stmt->bind_param("sssi", $nombre, $apellido, $email, $id_usuario);
    $stmt->execute();

    // Actualizar la tabla paciente
    $update_paciente = "UPDATE paciente SET telefono = ?, id_obra_social = ?, calle = ?, 
                        numero = ?, ciudad = ?, provincia = ?, codigo_postal = ? 
                        WHERE correo = ?";
    $stmt = $conn->prepare($update_paciente);
    $stmt->bind_param("sissssss", $telefono, $obra_social, $calle, $numero, $ciudad, $provincia, $codigo_postal, $email);
    $stmt->execute();

    echo "<script>alert('Información actualizada correctamente.'); window.location.href = 'panel_usuario.php';</script>";
    exit();
}

// Obtener todas las obras sociales disponibles
$obras_sociales = $conn->query("SELECT * FROM obra_social");

include('includes/header.php');
?>

<section class="container" style="margin-top: 20px;">
    <h2 style="text-align: center;">Modificar Información</h2>
    <form action="modificar_usuario.php" method="POST" style="max-width: 600px; margin: 0 auto;">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo $usuario['apellido']; ?>" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo $usuario['telefono'] ?? ''; ?>">

        <label for="obra_social">Obra Social:</label>
        <select name="obra_social" required>
            <option value="">Seleccione una obra social</option>
            <?php while ($obra = $obras_sociales->fetch_assoc()): ?>
                <option value="<?php echo $obra['id_obra_social']; ?>" 
                    <?php echo ($obra['id_obra_social'] == $usuario['id_obra_social']) ? 'selected' : ''; ?>>
                    <?php echo $obra['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="calle">Calle:</label>
        <input type="text" name="calle" value="<?php echo $usuario['calle'] ?? ''; ?>">

        <label for="numero">Número:</label>
        <input type="text" name="numero" value="<?php echo $usuario['numero'] ?? ''; ?>">

        <label for="ciudad">Ciudad:</label>
        <input type="text" name="ciudad" value="<?php echo $usuario['ciudad'] ?? ''; ?>">

        <label for="provincia">Provincia:</label>
        <input type="text" name="provincia" value="<?php echo $usuario['provincia'] ?? ''; ?>">

        <label for="codigo_postal">Código Postal:</label>
        <input type="text" name="codigo_postal" value="<?php echo $usuario['codigo_postal'] ?? ''; ?>">

        <br><br>
        <input type="submit" value="Guardar Cambios" class="btn">
        <a href="panel_usuario.php" class="btn" style="background-color: #aaa;">Cancelar</a>
    </form>
</section>

<?php include('includes/footer.php'); ?>
