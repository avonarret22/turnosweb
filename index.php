<?php include('includes/header.php'); ?>
<main>
    <section class="header-content container">
        <div class="header-txt">
            <h1>Medical Center Group</h1>
            <p>Especialistas en la gestión de turnos médicos somos la empresa líder, más de 20 años de eficiencia y practicidad nos respaldan.</p>
            <a href="turnos.php" class="btn-1">Agenda tu Turno</a>
        </div>
        <div class="header-img">
            <img src="images/left.png" alt="Imagen principal de la página">
        </div>
    </section>

    <section class="about container">
        <div class="about-img">
            <img src="images/about.png" alt="Imagen de la empresa">
        </div>
        <div class="about-txt">
            <h2>Nosotros</h2>
            <p>Somos especialistas en el área de consultas y turnos médicos, ofrecemos la más amplia variedad de profesionales a disposición para que puedas disfrutar de una salud plena. Nuestro servicio ofrece el turno más próximo con el profesional más idóneo para tu consulta.</p>
            <br>
            <p>Evita la demora en los turnos y utiliza nuestro servicio para poder acceder al turno más próximo con el mejor profesional disponible.</p>
        </div>
    </section>

    <main class="servicios">
        <h2>Servicios</h2>
        <div class="servicios-content container">
            <div class="servicio-1">
                <a href="turnos.php?especialidad=pediatria">
                    <i class="fa-sharp fa-solid fa-hospital-user"></i>
                    <h3>Pediatría</h3>
                </a>
            </div>

            <div class="servicio-1">
                <a href="turnos.php?especialidad=ginecologia">
                    <i class="fa-sharp fa-solid fa-stethoscope"></i>
                    <h3>Ginecología</h3>
                </a>
            </div>

            <div class="servicio-1">
                <a href="turnos.php?especialidad=dermatologia">
                    <i class="fa-solid fa-bed-pulse"></i>
                    <h3>Dermatología</h3>
                </a>
            </div>

            <div class="servicio-1">
                <a href="turnos.php?especialidad=cardiologia">
                    <i class="fa-solid fa-hospital"></i>
                    <h3>Cardiología</h3>
                </a>
            </div>
        </div>
    </main>
</main>
<?php include('includes/footer.php'); ?>


















