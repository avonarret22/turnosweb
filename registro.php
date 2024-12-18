<?php
include('includes/header.php');
include('db.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $nombre = trim($_POST['name']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $dni = trim($_POST['dni']);
    $telefono = trim($_POST['telefono']);
    $tipo_telefono = $_POST['tipo_telefono'];
    $obra_social = $_POST['obra_social'];
    $calle = trim($_POST['calle']);
    $numero = trim($_POST['numero']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validación de contraseñas coincidentes
    if ($password !== $confirm_password) {
        echo "<script>alert('Las contraseñas no coinciden');</script>";
    } else {
        // Verificar si el correo ya existe
        $check_email = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('El correo ya está registrado. Intente con otro.');</script>";
        } else {
            // Hashing de la contraseña
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Iniciar transacción
            $conn->begin_transaction();

            try {
                // Insertar en la tabla usuarios
                $insert_user = "INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, 'usuario')";
                $stmt_user = $conn->prepare($insert_user);
                $stmt_user->bind_param("ssss", $nombre, $apellido, $email, $hashed_password);
                $stmt_user->execute();

                // Obtener el ID del usuario insertado
                $id_usuario = $stmt_user->insert_id;

                // Insertar en la tabla paciente
                $insert_paciente = "INSERT INTO paciente (nombre, apellido, dni, telefono, tipo_telefono, id_obra_social, correo, calle, numero, ciudad, provincia, codigo_postal, id_usuario) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_paciente = $conn->prepare($insert_paciente);
                $stmt_paciente->bind_param("sssssisissssi", $nombre, $apellido, $dni, $telefono, $tipo_telefono, $obra_social, $email, $calle, $numero, $ciudad, $provincia, $codigo_postal, $id_usuario);
                $stmt_paciente->execute();

                // Confirmar transacción
                $conn->commit();

                echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.');</script>";
                header('Location: login.php');
                exit();
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $conn->rollback();
                echo "<script>alert('Error al registrar el usuario. Inténtalo de nuevo.');</script>";
            }
        }
    }
}
?>

<section class="formulario container">
    <form action="registro.php" method="POST" autocomplete="off">
        <h2>Registrarse</h2>

        <div class="input-container">
            <input type="text" name="name" placeholder="Nombre" required>
        </div>
        <div class="input-container">
            <input type="text" name="apellido" placeholder="Apellido" required>
        </div>
        <div class="input-container">
            <input type="text" name="dni" placeholder="DNI" required>
        </div>
        <div class="input-container">
            <input type="text" name="telefono" placeholder="Teléfono" required>
        </div>
        <div class="input-container">
            <select name="tipo_telefono" required>
                <option value="móvil">Móvil</option>
                <option value="fijo">Fijo</option>
            </select>
        </div>
        <div class="input-container">
            <select name="obra_social" required>
                <option value="">Seleccione su obra social</option>
                <?php
                $obras_sociales = $conn->query("SELECT id_obra_social, nombre FROM obra_social");
                while ($obra = $obras_sociales->fetch_assoc()) {
                    echo "<option value='" . $obra['id_obra_social'] . "'>" . $obra['nombre'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="input-container">
            <input type="email" name="email" placeholder="Correo" required>
        </div>
        <div class="input-container">
            <input type="text" name="calle" placeholder="Calle" required>
        </div>
        <div class="input-container">
            <input type="text" name="numero" placeholder="Número" required>
        </div>
        <div class="input-container">
            <input type="text" name="ciudad" placeholder="Ciudad" required>
        </div>
        <div class="input-container">
            <input type="text" name="provincia" placeholder="Provincia" required>
        </div>
        <div class="input-container">
            <input type="text" name="codigo_postal" placeholder="Código Postal" required>
        </div>
        <div class="input-container">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <div class="input-container">
            <input type="password" name="confirm-password" placeholder="Confirmar Contraseña" required>
        </div>
        <input type="submit" value="Registrar" class="btn">
    </form>
</section>

<?php include('includes/footer.php'); ?>
