<?php include('includes/header.php'); ?>

<?php
session_start();
include('db.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verificar si el usuario existe
    $query = "SELECT id_usuario, nombre, rol, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($password, $usuario['password'])) {
            // Guardar datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según el rol
            if ($usuario['rol'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: panel_usuario.php");
            }
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Correo no registrado'); window.location.href = 'login.php';</script>";
    }
}
?>


<section class="formulario container">
    <form action="login.php" method="POST" autocomplete="off">
        <h2>Iniciar Sesión</h2>
        <div class="input-container">
            <input type="email" name="email" placeholder="Correo" required>
        </div>
        <div class="input-container">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <input type="submit" value="Iniciar Sesión" class="btn">
    </form>
</section>

<?php include('includes/footer.php'); ?>
