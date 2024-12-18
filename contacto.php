<?php 
include('includes/header.php'); 
include('db.php'); // Conexión a la base de datos

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['name']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['phone']);
    $mensaje = trim($_POST['message']);
    $motivo = "Contacto"; // Puedes personalizar o agregar más motivos si lo deseas
    $fecha = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Insertar los datos en la tabla
    $query = "INSERT INTO datos (nombre, telefono, email, mensaje, motivo, fecha) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $nombre, $telefono, $email, $mensaje, $motivo, $fecha);

    if ($stmt->execute()) {
        echo "<script>alert('Mensaje enviado correctamente. Gracias por contactarnos.');</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje. Intente de nuevo.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<section class="contacto container">
    <form action="contacto.php" method="POST" autocomplete="off">
        <!-- Título dentro del formulario -->
        <h2>Formulario de Contacto</h2>

        <div class="input-container">
            <input type="text" name="name" placeholder="Nombre" required>
        </div>
        <div class="input-container">
            <input type="email" name="email" placeholder="Correo" required>
        </div>
        <div class="input-container">
            <input type="tel" name="phone" placeholder="Teléfono" required>
        </div>
        <div class="input-container">
            <textarea name="message" placeholder="Mensaje" required></textarea>
        </div>
        <input type="submit" value="Enviar Mensaje" class="btn">
    </form>

    <!-- Cuadro de información -->
    <div class="info-contacto">
        <h3>Información de Contacto</h3>
        <p><strong>Horarios de Atención:</strong><br> Lunes a Viernes de 9:00 AM a 18:00 PM</p>
        <p><strong>Días de Atención:</strong><br> Lunes a Viernes</p>
        <p><strong>Ubicación:</strong><br> AV.Corrientes 2037 CP 1001, Buenos Aires, Argentina</p>
        <p><strong>Teléfono de Contacto:</strong><br> +123 456 7890</p>
    </div>
</section>

<?php include('includes/footer.php'); ?>
