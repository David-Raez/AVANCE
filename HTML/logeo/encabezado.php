<?php
// C:\xampp\htdocs\AVANCE\HTML\logeo\encabezado.php
include("/xampp/htdocs/avance/HTML/conexion.php");
session_start();

// Definimos la variable $nombre_usuario con un valor por defecto.
$nombre_usuario = "Invitado";

// Verificamos si la sesión de usuario existe.
if (isset($_SESSION['user_id'])) {
    $usuario_sesion = $_SESSION['user_id'];

    // Usamos una consulta preparada para mayor seguridad.
    $query = "SELECT NOMBRES FROM usuario WHERE ID_USUARIO = ?";
    $stmt = mysqli_prepare($cn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $usuario_sesion);
        mysqli_stmt_execute($stmt);
        $rs_usuario = mysqli_stmt_get_result($stmt);
        
        if ($fila_usuario = mysqli_fetch_array($rs_usuario)) {
            $nombre_usuario = $fila_usuario[0];
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Si la sesión no está definida, redirigimos al login.
    header("Location: /avance/HTML/presentacion/frmLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/HTML/CSS/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <style>
        /* Estilos CSS para el menú desplegable */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #000000ff;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1ff;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Estilos generales del header */
        header#centrado {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #333;
            padding: 10px;
            flex-wrap: wrap; /* Permite que los elementos se envuelvan en pantallas pequeñas */
        }

        header#centrado a {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
        }

        /* Oculta los enlaces de navegación en dispositivos móviles por defecto */
        .nav-links {
            display: flex;
        }

        .menu-toggle {
            display: none; /* Oculta el botón de hamburguesa por defecto */
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        /* Media Query para pantallas pequeñas */
        @media (max-width: 768px) {
            header#centrado {
                justify-content: flex-start;
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-direction: column;
                display: none; /* Oculta el menú por defecto */
                width: 100%;
            }

            .nav-links.active {
                display: flex; /* Muestra el menú cuando tiene la clase 'active' */
            }

            .nav-links a {
                width: 100%;
                text-align: left;
                padding: 15px 0;
            }

            .menu-toggle {
                display: block; /* Muestra el botón de hamburguesa */
                align-self: flex-end; /* Alinea el botón a la derecha */
                margin-top: -30px; /* Ajusta la posición */
            }
        }
        /* Estilos para los enlaces del menú del encabezado */
        header a {
            text-decoration: none; /* Elimina el subrayado */
            color: blue;          /* Establece el color del texto */
            padding: 10px 15px;    /* Añade espacio alrededor del texto */
            display: inline-block; /* Permite aplicar padding y margin */
            font-weight: bold;     /* Hace el texto más visible */
            transition: background-color 0.3s ease; /* Transición suave al pasar el mouse */
        }

/* Estilos para cuando el mouse pasa por encima */
        header a:hover {
            background-color: #575757; /* Cambia el color de fondo al pasar el mouse */
            border-radius: 5px;        /* Añade esquinas redondeadas */
        }
    </style>
</head>
<body>
    <header id="centrado">
        <a href="/avance/HTML/logeo/bienvenido.php"><i class="ti ti-menu">MENU</i></a>
        <a href="/avance/HTML/presentacion/frmUsuario.php"><i class="ti ti-user"></i>USUARIO</a>
        <a href="/avance/HTML/PRESENTACION/frmAlumno.php"><i class="ti ti-friends"></i>ALUMNO</a>
        <a href="/avance/HTML/PRESENTACION/frmDocente.php"><i class="ti ti-chalkboard-teacher"></i>DOCENTES</a>
        <a href="/avance/HTML/presentacion/frmMatricula.php"><i class="ti ti-presentation-analytics"></i>MATRICULA</a>
        <a href="/avance/HTML/PRESENTACION/frmRecibo.php"><i class="ti ti-receipt"></i>RECIBO</a>
        <a href="/avance/HTML/PRESENTACION/frmFicha.php"><i class="ti ti-receipt"></i>FICHA</a>
        <a href="/avance/HTML/PRESENTACION/frmCarrera.php"><i class="ti ti-library">CARRERAS</i></a>
		<a href="/avance/HTML/PRESENTACION/frmModulo_Ocupacional.php"><i class="ti ti-book-2">MODULOS</i></a>
		<a href="/avance/HTML/PRESENTACION/frmFormacion.php"><i class="ti ti-brand-nuxt"></i>FORMACION</a>
        <a href="/avance/HTML/PRESENTACION/frmPago.php"><i class="ti ti-cash"></i>PAGOS</a>

        <div class="dropdown">
            <small>Hola, <?php echo $nombre_usuario; ?></small>
            <div class="dropdown-content">
                <a href="/avance/HTML/PRESENTACION/frmPerfil.php" class="dropdown-item notify-item">
                    <i class="fa fa-user"></i> <span>Cambiar Contraseña</span>
                </a>
                
                <a href="../PRESENTACION/frmLogin.php" class="dropdown-item notify-item">
                    <i class="fa fa-power-off"></i> <span>Salir</span>
                </a>
            </div>
        </div>
    </header>
</body>
</html>