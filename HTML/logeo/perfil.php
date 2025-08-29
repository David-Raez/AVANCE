<?php
    $cn = mysqli_connect("localhost","root","");
    mysqli_select_db($cn,"cetpro");

// Esta página es para cualquier usuario logueado, no requiere un rol específico

// También puedes usar checkUserRole para forzar un rol específico si es necesario
// if (!checkUserRole($cn, 3)) { /* ... */ } // Si solo el Usuario Normal puede ver su perfil
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil (Usuario)</title>
    <link rel="stylesheet" href="../../CSS/login.css">
    <style>
        .page-content {
            padding: 40px;
            text-align: center;
        }
        .page-content h2 { color: #17a2b8; margin-bottom: 20px; }
        .page-content p { font-size: 1.1em; color: #666; }
    </style>
</head>
<body>
    <main>
        <header>
            <img src="/avance/HTML/images/cetpro.png" alt="Logo de CETPRO CNTSMP">
            <a href="https://www.cetprocntsmp.edu.pe/#">Volver a la página principal</a>
        </header>
        <nav>
            <a href="/avance/HTML/logeo/bienvenido.php">Volver al Panel</a> | <a href="/avance/HTML/logeo/logout.php">Cerrar Sesión</a>
        </nav>
        <section class="page-content">
            <h2>Mi Perfil</h2>
            <p>¡Todos los usuarios logueados pueden ver esta página!</p>
            <p>Información del perfil de **<?php echo htmlspecialchars($_SESSION['NOMBRES']); ?>**.</p>
        </section>
    </main>
</body>
</html>
<?php mysqli_close($cn); ?>