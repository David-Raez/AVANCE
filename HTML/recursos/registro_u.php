<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        

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
        <select name="selDOC" id="selDOC">
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
            $stmt_check = $cn->prepare("SELECT ID_USUARIO FROM usuario WHERE N_DOCUMENTO = $num_documento LIMIT 1");
            $stmt_check->bind_param("s", $num_documento);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errors[] = "El número de documento ya está registrado.";
            }
            $stmt_check->close();
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $cn->prepare("INSERT INTO usuario (TIPO_DOC, N_DOCUMENTO, NOMBRES, APE_PATERNO, APE_MATERNO, PASSWORD, TIPO_CONTRATO,N_RD,  ID_ROL,CELULAR,SEXO,FECHA_NACIMIENTO) VALUES ('$tipo_documento', '$num_documento', '$nombres', '$apellido_paterno', '$apellido_materno','$hashed_password', '$tipo_contrato', '$RD','$rol','$celular','$sexo','$nac')");
        // "sssssssss" - 9 's' para 9 parámetros de tipo string (o null para email)
        // El email se trata como 's' porque PDO/MySQLi puede manejar NULL cuando el tipo esperado es string.
        $stmt->bind_param("sssssssss", $tipo_documento, $num_documento, $nombres, $apellido_paterno, $apellido_materno, $username_for_db,$hashed_password, $tipo_contrato, $RD,$rol,$celular,$sexo,$nac);

        if ($stmt->execute()) {
            // Registro exitoso, redirigir con mensaje de éxito
            header("Location: usuario.php?status=success");
            exit();
        } else {
            // Error al insertar
            $error_message = "Error al registrar el usuario: " . $stmt->error;
            header("Location: registro_usuario.php?status=error&message=" . urlencode($error_message));
            exit();
        }

        // Cerrar la sentencia
        $stmt->close();
    ?>

</body>
</html>