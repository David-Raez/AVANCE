<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="">
    
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <?php
        
            // Mostrar mensajes de éxito o error si existen
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<p class="message success">¡Registro exitoso! Ya puedes iniciar sesión.</p>';
                } elseif ($_GET['status'] == 'error') {
                    $error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Ocurrió un error al registrar el usuario.';
                    echo '<p class="message error">' . $error_message . '</p>';
                }
            }
        ?>

    <form method="POST" action="/HTML/MODULOS/registro_usuario.php">
        <label for="username">ROL</label>
        <select name="selROL">
            <option value="1">ADMINISTRADOR</option>
            <option value="2">DIRECTOR</option>
            <option value="3">ADMINISTRATIVOS</option>
            <option value="4">DOCENTE</option>
            <option value="5">ALUMNO</option>
        </select><br><br>

        <label for="username">TIPO DOCUMENTO:</label><br>
        <select name="selDOC">
            <option value="dni">DNI</option>
            <option value="ce">CE</option>
            <option value="dni">CPP</option>
        </select><br><br>

        <label for="username">N° DOCUMENTO:</label><br>
        <input type="text" name="txt_documento" required><br><br>

        <label for="username">NOMBRES:</label><br>
        <input type="text" name="username" required><br><br>

        <label for="username">APELIIDO PATERNO:</label><br>
        <input type="text"  name="txt_paterno" required><br><br>

        <label for="username">APELLIDO MATERNO:</label><br>
        <input type="text" name="txt_materno" required><br><br>

        <label for="fecha_nac">FECHA NACIMIENTO</label><br>
        <input type="date" name="nac"><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" name="password" required><br><br>


        <label for="password">Contraseña:</label><br>
        <input type="password" name="confirm_password" required><br><br>


        <label for="username">TIPO CONTRATO:</label><br>
        <select name="selTIPO_CONTRATO">
            <Option value="nombrado">NOMBRADO</Option>
            <Option value="contratado">CONTRATADO</Option>
            <Option value="ninguno">NINGUNO</Option>
        </select><br><br>


        <input type="submit" value="Registrarse">
    </form>

    
        
    </div>
        <?php 
        $cn=mysqli_connect("localhost","root","");
        mysqli_select_db($cn,"cetpro");



        // 2. Procesar el formulario cuando se envía por POST
    //if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener y sanear los datos del formulario
        $tipo_documento = trim($_POST['selDOC']);
        $num_documento = trim($_POST['txt_documento']);
        $nombres = trim($_POST['txt_nombres']);
        $apellido_paterno = trim($_POST['txt_paterno']);
        $apellido_materno = trim($_POST['txt_materno']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password']; // Nuevo campo para confirmar contraseña
        $tipo_contrato = trim($_POST['selTIPO_CONTRATO']);
        $rol=trim($_POST['selROL']);
        $nac=trim($_POST['nac']);

        // 3. Validaciones básicas
        $errors = [];

        // Validación de campos obligatorios
        if (empty($tipo_documento)) $errors[] = "El tipo de documento es requerido.";
        if (empty($num_documento)) $errors[] = "El número de documento es requerido.";
        if (empty($nombres)) $errors[] = "El nombre es requerido.";
        if (empty($apellido_paterno)) $errors[] = "El apellido paterno es requerido.";
        if (empty($apellido_materno)) $errors[] = "El apellido materno es requerido.";
        if (empty($password)) $errors[] = "La contraseña es requerida.";
        if (empty($confirm_password)) $errors[] = "Debe confirmar la contraseña.";
        if (empty($tipo_contrato)) $errors[] = "El tipo de contrato es requerido.";
        if (empty($rol)) $errors[] = "El tipo de rol es requerido.";
        if (empty($nac)) $errors[] = "La fecha de nacimiento del usuario.";
        

        // Validaciones de formato y longitud
        switch ($tipo_documento) {
        case 'dni':
        if (strlen($num_documento) !== 8) { // Usamos !== para asegurar que sea exactamente 8
            $errors[] = "El número de DNI debe tener exactamente 8 caracteres.";
        }
        // Puedes añadir más validaciones específicas para DNI, por ejemplo, que sean solo números:
        if (!ctype_digit($num_documento)) {
            $errors[] = "El número de DNI solo puede contener dígitos.";
        }
        break;
        }
        if (strlen($num_documento) < 6 || strlen($num_documento) > 20) { // Ejemplo de longitud
            $errors[] = "El número de documento debe tener entre 6 y 20 caracteres.";
        }
        if (!preg_match("/^[-_\ÁÉÍÓÚ@]+$/", $nombres)) {
            $errors[] = "El nombre solo puede contener letras y espacios.";
        }
        if (!preg_match("/^[-_\ÁÉÍÓÚ@]+$/", $apellido_paterno)) {
            $errors[] = "El apellido paterno solo puede contener letras y espacios.";
        }
        if (!preg_match("/^[-_\ÁÉÍÓÚ]+$/", $apellido_materno)) {
            $errors[] = "El apellido materno solo puede contener letras y espacios.";
        }

        if (($password)) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres.";
        }
        if ($password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        // Si no hay errores de validación iniciales, verificar unicidad del número de documento y email (si se proporciona)
        if (empty($errors)) {
            // Verificar si el número de documento ya existe
            $stmt_check = $cn->prepare("SELECT ID_USUARIO FROM usuario WHERE N_DOCUMENTO = $num_documento LIMIT 1");
            $stmt_check->bind_param("s", $num_documento);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errors[] = "El número de documento ya está registrado.";
            }
            $stmt_check->close();
        }

        // Si hay errores, redirigir de nuevo al formulario con los errores
        if (!empty($errors)) {
            $error_message = implode(" ", $errors); // Unimos los errores con saltos de línea para mejor lectura
            header("Location: registro_usuario.php?status=error&message=" . urlencode($error_message));
            exit();
        }

        // 4. Hashear la contraseña (¡MUY IMPORTANTE!)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. Insertar los datos en la base de datos
        // Usamos el campo 'nombres' del formulario para el campo 'nombres' de la DB
        // Creamos un 'username' a partir del número de documento o alguna otra lógica
        // Para simplificar, asumiré que 'nombres' de tu formulario es el campo 'nombres' en la DB
        // y que necesitamos un 'username' para la DB. Podríamos usar el num_documento como username.
        // O si tu "NOMBRES" en el formulario es el nombre de usuario de login, podríamos ajustarlo.
        // Para este ejemplo, usaré el 'num_documento' como 'username' de la DB para garantizar unicidad.
        $username_for_db = $num_documento; // Puedes ajustar esto según tu lógica de negocio

        // Si el email está vacío, lo insertamos como NULL en la base de datos

        $sql = "INSERT INTO usuario (TIPO_DOC, N_DOCUMENTO, NOMBRES, APE_PATERNO, APE_MATERNO, password, tipo_contrato, N_RD, ID_ROL, CELULAR, SEXO, FECHA_NACIMIENTO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $cn->prepare($sql);

    // Verificar si la preparación de la sentencia falló
    if ($stmt === false) {
        $error_message = "Error al preparar la consulta: " . $cn->error;
        header("Location: registro_usuario.php?status=error&message=" . urlencode($error_message));
        exit();
    }

    // 4. Vincular los parámetros
    // La cadena de tipos debe tener el mismo número de caracteres que marcadores de posición (?)
    // 's' para string, 'i' para integer, 'd' para double
    // Contamos 12 parámetros en el INSERT, por lo tanto, necesitamos 12 's' si todos son strings.
    // Ajusta los tipos ('s', 'i', 'd') según el tipo de dato real de cada columna en tu DB.
    // Por ejemplo, si ID_ROL es un entero, usa 'i' para ese parámetro.
    $stmt->bind_param("ssssssssssss", // 12 parámetros: s (TIPO_DOC), s (N_DOCUMENTO), s (NOMBRES), s (APE_PATERNO), s (APE_MATERNO), s (password HASH), s (tipo_contrato), i (N_RD si es INT), s (ID_ROL si es string), s (CELULAR), s (SEXO), s (FECHA_NACIMIENTO)
        $tipo_documento,
        $num_documento,
        $nombres,
        $apellido_paterno,
        $apellido_materno,
        $hashed_password, // Usa la contraseña hasheada aquí
        $tipo_contrato,
        $RD,
        $rol,
        $celular,
        $sexo,
        $nac
    );

    // Verificar si el bind_param falló
    if ($bind_success === false) {
         $error_message = "Error al vincular parámetros: " . $stmt->error;
         header("Location: registro_usuario.php?status=error&message=" . urlencode($error_message));
         exit();
    }


    // 5. Ejecutar la sentencia
    if ($stmt->execute()) {
        // Registro exitoso
        header("Location: usuario.php?status=success"); // Redirigir a tu página de éxito, que podría ser usuario.php
        exit();
    } else {
        // Error al insertar
        $error_message = "Error al registrar el usuario: " . $stmt->error; // Muestra el error específico de MySQL
        header("Location: registro_usuario.php?status=error&message=" . urlencode($error_message));
        exit();
    }

    // 6. Cerrar la sentencia y la conexión
    $stmt->close();
    $cn->close();
//} 
    //else {
    // Si la solicitud no es POST, redirigir o mostrar un mensaje de error
    //header("Location: registro_usuario.php?status=error&message=" . urlencode("Acceso no autorizado."));
//    exit();
//}

        

    ?>
</body>
</html>