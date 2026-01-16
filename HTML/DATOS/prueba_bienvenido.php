<?php


// Incluye la conexión a la base de datos y funciones de ayuda
require_once '../conexion.php';

// Redirigir si el usuario no está logueado


// Obtener los ítems de menú para el rol del usuario logueado
$user_role_id = $_SESSION['NOMB_ROL'];
$menu_items = getMenuItemsByRoleId($cn, $user_role_id);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - <?php echo htmlspecialchars($_SESSION['username']); ?></title>
    <link rel="stylesheet" href="../../CSS/login.css"> <style>
        /* Estilos específicos para el panel de bienvenida y menú de navegación */
        .welcome-content {
            padding: 40px;
            text-align: center;
            flex-grow: 1; /* Para que ocupe el espacio restante */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .welcome-content h2 {
            color: #007bff;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .welcome-content p {
            font-size: 1.1em;
            color: #555;
            line-height: 1.6;
            max-width: 600px;
            margin-bottom: 30px;
        }

        /* Estilos del menú de navegación */
        nav {
            background-color: #333; /* Fondo oscuro para la barra de navegación */
            padding: 10px 0;
            border-top: 2px solid #555;
            border-bottom: 2px solid #555;
            display: flex;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap; /* Permite que los elementos se envuelvan en pantallas pequeñas */
            justify-content: center;
        }
        nav ul li {
            margin: 8px 15px; /* Espacio entre los ítems de menú */
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            border-radius: 25px; /* Bordes más redondeados para los botones del menú */
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
            font-size: 1.05em;
            background-color: #007bff; /* Color por defecto de los botones del menú */
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        nav ul li a:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
            transform: translateY(-2px);
        }
        nav ul li a.logout-btn { /* Estilo específico para el botón de cerrar sesión */
            background-color: #dc3545; /* Rojo para cerrar sesión */
        }
        nav ul li a.logout-btn:hover {
            background-color: #c82333;
        }

        /* Ajustes responsivos para el menú */
        @media (max-width: 600px) {
            nav ul li {
                width: 100%; /* Cada botón de menú ocupa todo el ancho */
                margin: 5px 0;
            }
            nav ul li a {
                text-align: center;
                border-radius: 8px; /* Menos redondeado en móvil */
            }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <img src="/CETPRO/HTML/images/cetpro.png" alt="Logo de CETPRO CNTSMP">
            <a href="https://www.cetprocntsmp.edu.pe/#">Volver a la página principal</a>
        </header>

        <nav>
            <ul>
                <li><a href="/CETPRO/HTML/logeo/bienvenido.php">Inicio</a></li>
                <?php
                // Generar dinámicamente los ítems de menú basados en los permisos del rol
                foreach ($menu_items as $item) {
                    echo '<li><a href="' . htmlspecialchars($item['url_item']) . '">' . htmlspecialchars($item['nombre_item']) . '</a></li>';
                }
                ?>
                <li><a href="/CETPRO/HTML/logeo/logout.php" class="logout-btn">Cerrar Sesión</a></li>
            </ul>
        </nav>

        <section class="welcome-content">
            <h2>¡Bienvenido al Panel de Control!</h2>
            <p>Hola, **<?php echo htmlspecialchars($_SESSION['username']); ?>**.</p>
            <p>Tu rol es **<?php
                // Opcional: Mostrar el nombre del rol en lugar del ID
                $role_name = "Desconocido";
                $stmt_role = mysqli_prepare($cn, "SELECT nombre_rol FROM roles WHERE id_rol = ?");
                if ($stmt_role) {
                    mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_role_id']);
                    mysqli_stmt_execute($stmt_role);
                    $result_role = mysqli_stmt_get_result($stmt_role);
                    if ($row_role = mysqli_fetch_assoc($result_role)) {
                        $role_name = $row_role['nombre_rol'];
                    }
                    mysqli_stmt_close($stmt_role);
                }
                echo htmlspecialchars($role_name);
            ?>**.</p>
            <p>Utiliza el menú de navegación para acceder a las secciones permitidas según tu rol.</p>
        </section>
    </main>

    <?php
    // Cierra la conexión a la base de datos al final del script
    if (isset($cn) && is_object($cn)) {
        mysqli_close($cn);
    }
    ?>
</body>
</html>