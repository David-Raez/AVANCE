<?php
	$cn=mysqli_connect("localhost","root","");
	mysqli_select_db($cn,"avance");



	$acceso=""; 
	$message="";

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoge y limpia los datos enviados por el usuario
    $username = trim($_POST['txt_usuario'] ?? '');
    $password = trim($_POST['txt_password'] ?? '');

    // Validación básica: comprueba campos no vacíos
    if (empty($username) || empty($password)) {
        $message = "Por favor, introduce tu nombre de usuario y contraseña.";
    } else {
        // --- INICIO DE LA LÓGICA DE VERIFICACIÓN CON MYSQLI ---
        // 1. Prepara la consulta SQL para prevenir inyecciones SQL
        // Busca el ID, nombre de usuario y el hash de la contraseña en tu tabla 'usuarios'
        // ¡Asegúrate de que el nombre de tu tabla sea 'usuarios' o el que uses!
        $query = "SELECT id, username, password_hash FROM usuarios WHERE username = ?";
        $stmt = mysqli_prepare($cn, $query);

        if ($stmt) {
            // 2. Vincula el parámetro (el nombre de usuario) a la consulta preparada
            mysqli_stmt_bind_param($stmt, "s", $username); // "s" indica que el parámetro es una cadena (string)

            // 3. Ejecuta la consulta preparada
            mysqli_stmt_execute($stmt);

            // 4. Obtiene el resultado de la consulta
            $result = mysqli_stmt_get_result($stmt);

            // 5. Verifica si se encontró un usuario
            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result); // Obtiene los datos del usuario como un array asociativo

                // 6. Verifica la contraseña usando password_verify()
                if (password_verify($password, $user['password_hash'])) {
                    // Contraseña correcta: el usuario está autenticado.

                    // Establece las variables de sesión
                    $_SESSION['loggedin'] = true;
                    $_SESSION['N_DOCUMENTO'] = $user['N_DOCUMENTO'];
                    $_SESSION['username'] = $user['username'];

                    // Redirige al usuario al dashboard o página protegida
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $message = "Nombre de usuario o contraseña inválidos.";
                }
            } else {
                $message = "Nombre de usuario o contraseña inválidos.";
            }

            // Cierra la sentencia preparada
            mysqli_stmt_close($stmt);
        } else {
            $message = "Error en la preparación de la consulta: " . mysqli_error($cn);
            // En producción: error_log("Error de preparación MySQLi: " . mysqli_error($cn));
        }
        // --- FIN DE LA LÓGICA DE VERIFICACIÓN CON MYSQLI ---
    	}
	}
	
	if ($usuario=="administrador" && $password=="12345") {
		$acceso="ok";
	}
	if ($usuario=="sistemas" && $password=="67890") {
		$acceso="ok";
	}
	if ($usuario=="operador" && $password=="13579") {
		$acceso="ok";
	}
	
?>
