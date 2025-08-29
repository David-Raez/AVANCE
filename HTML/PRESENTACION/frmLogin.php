<?php 
    session_start();
    
    include("/xampp/htdocs/avance/HTML/DATOS/login.php");
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CETPRO CNTSMP</title>
    <style>
        /* --- Estilos Generales del Cuerpo y Contenedor Principal --- */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Fuente moderna y legible */
            background: linear-gradient(135deg, #74b9ff, #007bff); /* Degradado de fondo azul */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ocupa toda la altura de la vista */
            color: #333; /* Color de texto por defecto */
        }

        main {
            background-color: #ffffff; /* Fondo blanco para el contenido principal */
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); /* Sombra más pronunciada */
            width: 90%;
            max-width: 900px; /* Ancho máximo para la página de login */
            display: flex;
            flex-direction: column; /* Columna para que header y section estén uno sobre otro */
            overflow: hidden; /* Asegura que los bordes redondeados se apliquen bien */
        }

        /* --- Estilos del Encabezado (Header) --- */
        header {
            background-color: #0056b3; /* Un azul más oscuro para el encabezado */
            padding: 20px;
            display: flex;
            justify-content: space-between; /* Espacio entre la imagen y el enlace */
            align-items: center;
            border-bottom: 2px solid #004085; /* Línea separadora */
        }

        header img {
            height: 60px; /* Tamaño del logo */
            width: auto;
            border-radius: 50%; /* Si quieres que el logo sea circular */
            object-fit: cover;
            border: 3px solid #ffffff; /* Borde blanco alrededor del logo */
        }

        header a {
            color: #ffffff; /* Texto blanco para el enlace */
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        header a:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Fondo semitransparente al pasar el ratón */
            transform: scale(1.05); /* Ligeramente más grande al pasar el ratón */
        }

        /* --- Estilos de la Sección del Formulario --- */
        .login-section {
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1; /* Permite que la sección ocupe el espacio restante */
        }

        .login-form {
            background-color: #f9f9f9; /* Fondo ligeramente gris para el formulario */
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px; /* Ancho máximo para el formulario */
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        .login-form h2 {
            margin-bottom: 30px;
            color: #007bff; /* Color azul para el título "INICIAR SESION" */
            font-size: 2.2em;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase; /* Todo en mayúsculas */
        }

        /* --- Estilos para los Grupos de Entrada (Label + Input) --- */
        .input-group {
            margin-bottom: 25px; /* Más espacio entre grupos */
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            color: #444;
            font-weight: 600;
            font-size: 1.05em;
        }

        /* IMPORTANTE: Hemos cambiado la selección aquí para incluir input[type="text"] */
        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: calc(100% - 24px); /* Ancho completo menos el padding y borde */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1.05em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff; /* Fondo blanco para los campos */
        }

        /* IMPORTANTE: Hemos cambiado la selección aquí para incluir input[type="text"] */
        .input-group input[type="text"]:focus,
        .input-group input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.4);
        }

        /* --- Estilos del Botón de Enviar --- */
        .submit-button {
            background-color: #28a745; /* Un verde vibrante para el botón de ingresar */
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2em;
            font-weight: 700;
            width: 100%;
            margin-top: 20px; /* Espacio superior */
            transition: background-color 0.3s ease, transform 0.2s ease;
            letter-spacing: 0.5px;
        }

        .submit-button:hover {
            background-color: #218838; /* Verde más oscuro al pasar el ratón */
            transform: translateY(-2px); /* Ligeramente elevado al pasar el ratón */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .submit-button:active {
            transform: translateY(0); /* Vuelve a su posición original al hacer clic */
            background-color: #1e7e34;
        }

        /* --- Estilos para Mensajes de Error --- */
        .error-message {
            color: #dc3545; /* Rojo para los mensajes de error */
            font-weight: 600;
            margin-top: 20px;
            background-color: #ffe0e0; /* Fondo rojo claro */
            border: 1px solid #dc3545;
            padding: 10px;
            border-radius: 5px;
            animation: fadein 0.5s; /* Animación de aparición */
        }

        @keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* --- Estilos Responsivos Básicos --- */
        @media (max-width: 768px) {
            main {
                width: 95%;
                margin: 20px;
            }

            .login-section {
                padding: 25px;
            }

            .login-form {
                padding: 25px;
            }

            header {
                flex-direction: column; /* Apila los elementos del header en pantallas pequeñas */
                text-align: center;
            }

            header img {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .login-form h2 {
                font-size: 1.8em;
            }

            .input-group label,
            .input-group input,
            .submit-button { /* Se ha quitado 'select' de esta regla */
                font-size: 0.95em;
                padding: 10px;
            }

            .error-message {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <img src="/avance/HTML/images/cetpro.png" alt="Logo de CETPRO CNTSMP"> <a href="https://www.cetprocntsmp.edu.pe/#">Volver a la página principal</a>
        </header>
        <section class="login-section">
            <form method="post" class="login-form" action="frmLogin.php">
                <h2>INICIAR SESION</h2>
                <p class="input-group">
                    <label for="txt_dni">DNI del Usuario:</label>
                    <input type="text" name="txt_dni" id="txt_dni" placeholder="Ingresa tu DNI" required>
                </p>
                <p class="input-group">
                    <label for="txt_password">Contraseña:</label>
                    <input type="password" name="txt_password" id="txt_password" placeholder="Ingresa tu contraseña" required>
                </p>
                <input type="submit" name="btn_ingresar" value="INGRESAR" class="submit-button">
                <?php
                // Muestra el mensaje de error si existe en la sesión
                if (isset($_SESSION['login_error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
                    unset($_SESSION['login_error']); // Borra el error para que no se muestre de nuevo
                }
                ?>
                <?php 
			        require('../logeo/pie.php'); 
		        ?>
            </form>
        </section>
    </main>
</body>
</html>
<?php
// Cierra la conexión a la DB solo al final del script.
if (isset($cn) && is_object($cn)) {
    mysqli_close($cn);
}
?>