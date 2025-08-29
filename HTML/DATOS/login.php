<?php
 // ¡ESTO DEBE SER LA PRIMERA LÍNEA EJECUTABLE DEL ARCHIVO!

// --- Configuración de la conexión a la base de datos ---
include '../conexion.php';

// --- Lógica de procesamiento del formulario (si se envió por POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CAMBIO AQUÍ: Ahora recibimos el DNI del input de texto
    $user_dni_input = $_POST['txt_dni'];     // DNI ingresado por el usuario
    $password_input = $_POST['txt_password']; // Contraseña ingresada por el usuario

    // CAMBIO AQUÍ: La consulta debe buscar por N_DOCUMENTO
    $stmt = mysqli_prepare($cn, "SELECT ID_USUARIO, N_DOCUMENTO_USUARIO, CLAVE FROM usuario WHERE N_DOCUMENTO_USUARIO = ?");

    if ($stmt === false) {
        error_log("Error al preparar la consulta de login: " . mysqli_error($cn));
        $_SESSION['login_error'] = "Hubo un error interno. Intente más tarde.";
        header("Location: login.php");
        exit();
    }

    // CAMBIO AQUÍ: Vincula el DNI. "s" indica que el parámetro es de tipo string.
    mysqli_stmt_bind_param($stmt, "s", $user_dni_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Verifica si se encontró un usuario
    if (mysqli_num_rows($result) == 1) {
        $user_db_data = mysqli_fetch_assoc($result); // Datos del usuario desde la DB

        // 3. Verificar la contraseña hasheada
        if (password_verify($password_input, $user_db_data['CLAVE'])) {
            // Contraseña correcta: Iniciar sesión y redirigir
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_db_data['ID_USUARIO'];
            $_SESSION['username'] = $user_db_data['N_DOCUMENTO']; // Puedes usar N_DOCUMENTO como username

            // *** REDIRECCIÓN EXITOSA ***
            header("Location: /avance/HTML/logeo/bienvenido.php"); // Usa la ruta absoluta correcta
            exit(); // ¡FUNDAMENTAL! Detiene la ejecución del script.

        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "DNI o Contraseña incorrectos."; // Mensaje genérico por seguridad
            header("Location: frmLogin.php"); // Redirige a la misma página para mostrar error
            exit();
        }
    } else {
        // Usuario no encontrado
        $_SESSION['login_error'] = "DNI o Contraseña incorrectos."; // Mensaje genérico por seguridad
        header("Location: frmLogin.php"); // Redirige a la misma página para mostrar error
        exit();
    }

    mysqli_stmt_close($stmt); // Cierra el statement preparado
}

// Nota: La conexión $cn se cierra al final del script.
?>

