<?php 
// /xampp/htdocs/AVANCE/HTML/logeo/bienvenido.php
include("encabezado.php");
// Verifica si el usuario no ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no está logueado, redirige al formulario de login
    header("Location: ../presentacion/frmLogin.php");
    exit();
}

// Incluye el encabezado que contiene la lógica de sesión y el menú


// Asumiendo que 'nombre_usuario' se ha establecido en el archivo encabezado.php
$nombre_usuario_mostrado = $nombre_usuario ?? 'Usuario';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BIENVENIDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .dashboard {
            text-align: center;
            padding: 50px 20px;
        }
        .dashboard h1 {
            color: #4a90e2;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .dashboard p {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 40px;
        }
        .quick-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .quick-links a {
            text-decoration: none;
            color: #fff;
            background-color: #50e3c2;
            padding: 20px 30px;
            border-radius: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .quick-links a:hover {
            background-color: #2fb59c;
        }
    </style>
</head>
<body>
    <header>
        <h1>¡Hola, <?php echo htmlspecialchars($nombre_usuario_mostrado); ?>!</h1>
        <p>Bienvenido a tu panel de control. Aquí puedes gestionar tu información y acceder a las principales funcionalidades del sistema.</p>
    </header>
    
    <main class="dashboard">
        
        
        <div class="quick-links">
            <a href="/avance/HTML/PRESENTACION/frmRecibo.php">
                <i class="ti ti-cash"></i> <br> Mis Pagos
            </a>
            <a href="/avance/HTML/presentacion/frmMatricula.php">
                <i class="ti ti-presentation-analytics"></i> <br> Mis Matrículas
            </a>
            <a href="/avance/HTML/PRESENTACION/frmPerfil.php">
                <i class="ti ti-user"></i> <br> Mi Perfil
            </a>
        </div>
        
    </main>
    
    <footer>
        <?php 
            require('pie.php'); 
        ?>
    </footer>
</body>
</html>