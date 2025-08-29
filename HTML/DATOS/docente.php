<?php
// /xampp/htdocs/CETPRO/HTML/DATOS/docente.php
session_start();
require_once '/xampp/htdocs/avance/HTML/conexion.php';

// Mostrar errores en desarrollo. ¡Desactivar para producción!
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Lógica para procesar las peticiones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- LÓGICA DE BÚSQUEDA ---
    if (isset($_POST['btn_buscar'])) {
        $tipo_busqueda = $_POST['selTipo_busqueda'] ?? '';
        $valor = $_POST['txt_valor'] ?? '';
        
        // Limpia las variables de sesión para evitar mostrar datos antiguos
        unset($_SESSION['docente_encontrado']);
        unset($_SESSION['docente_data']);
        unset($_SESSION['listado_docentes']);
        unset($_SESSION['mostrar_form_nuevo']);

        if (empty($valor)) {
            $_SESSION['registration_error'] = "No hay datos a buscar.";
        } else {
            while (mysqli_more_results($cn) && mysqli_next_result($cn));
            
            $query = ($tipo_busqueda == '0') ? "CALL sp_buscadocente_por_nombre(?)" : "CALL sp_buscadocente_pordni(?)";
            
            $stmt = mysqli_prepare($cn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $valor);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $docente_data = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if ($docente_data) {
                    $_SESSION['docente_encontrado'] = true;
                    $_SESSION['docente_data'] = $docente_data;
                } else {
                    $_SESSION['registration_error'] = "El docente no existe o el valor ingresado es incorrecto.";
                }
            } else {
                $_SESSION['registration_error'] = "Error al preparar la búsqueda: " . mysqli_error($cn);
            }
        }
        header("Location: /avance/HTML/PRESENTACION/frmDocente.php");
        exit();
    }
    
    // --- LÓGICA DE LISTAR ---
    if (isset($_POST['btn_listar'])) {
        // Limpia las variables de sesión para evitar mostrar datos antiguos
        unset($_SESSION['docente_encontrado']);
        unset($_SESSION['docente_data']);
        unset($_SESSION['listado_docentes']);
        unset($_SESSION['mostrar_form_nuevo']);

        while (mysqli_more_results($cn) && mysqli_next_result($cn));
        $rs = mysqli_query($cn, "CALL sp_listadodocentes");
        if ($rs) {
            $listado_docentes = [];
            while ($row = mysqli_fetch_array($rs)) {
                $listado_docentes[] = $row;
            }
            mysqli_free_result($rs);
            $_SESSION['listado_docentes'] = $listado_docentes;
        } else {
            $_SESSION['registration_error'] = "Error al cargar la tabla de docentes: " . mysqli_error($cn);
        }
        header("Location: /avance/HTML/PRESENTACION/frmDocente.php");
        exit();
    }
    
    // --- LÓGICA DE NUEVO REGISTRO ---
    if (isset($_POST['btn_nuevo'])) {
        // Limpia las variables de sesión para evitar mostrar datos antiguos
        unset($_SESSION['docente_encontrado']);
        unset($_SESSION['docente_data']);
        unset($_SESSION['listado_docentes']);

        // Establece la variable de sesión para mostrar el formulario vacío
        $_SESSION['mostrar_form_nuevo'] = true;
        
        header("Location: /avance/HTML/PRESENTACION/frmDocente.php");
        exit();
    }
    if (isset($_POST['btn_seleccionar_editar'])) {
        limpiar_sesion();
        $documento_selec = $_POST['rd_elegir'] ?? '';

        while (mysqli_more_results($cn) && mysqli_next_result($cn));
        $query = "CALL sp_eliminadocente(?)";
        $stmt = mysqli_prepare($cn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $documento_selec);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $alumno_data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($alumno_data) {
                $_SESSION['alumno_data'] = $alumno_data;
                $_SESSION['alumno_encontrado'] = true;
                $_SESSION['registration_success'] = "Alumno seleccionado para edición.";
            } else {
                $_SESSION['registration_error'] = "Error al seleccionar el alumno o no encontrado.";
            }
        } else {
            $_SESSION['registration_error'] = "Error al preparar la selección: " . mysqli_error($cn);
        }
        header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
        exit();
    }

    // --- LÓGICA PARA GUARDAR, ACTUALIZAR Y ELIMINAR ---
    if (isset($_POST['btn_guardar']) || isset($_POST['btn_editar']) || isset($_POST['btn_borrar'])) {
        // Limpia las variables de sesión para evitar mostrar datos antiguos
        unset($_SESSION['docente_encontrado']);
        unset($_SESSION['docente_data']);
        unset($_SESSION['listado_docentes']);
        unset($_SESSION['mostrar_form_nuevo']);
        
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $errors = [];

        if (isset($_POST['btn_guardar'])) {
            if (empty($password) || $password !== $confirm_password) {
                $errors[] = "Las contraseñas no coinciden o están vacías.";
            } elseif (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
        } elseif (isset($_POST['btn_editar']) && !empty($password)) {
            if ($password !== $confirm_password) {
                $errors[] = "Las contraseñas no coinciden.";
            } elseif (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
        }

        $documento_antiguo = $_POST['txt_documento_antiguo'] ?? '';
        $tipo = $_POST['selTIPO'] ?? '';
        $documento = $_POST['txt_documento'] ?? '';
        $pat = $_POST['txt_paterno'] ?? '';
        $mat = $_POST['txt_materno'] ?? '';
        $nom = $_POST['txt_nombres'] ?? '';
        $tipo_contrato = $_POST['selTIPO_CONTRATO'] ?? '';
        $rd = $_POST['txt_rd'] ?? '';
        $sexo = $_POST['rdoSEXO'] ?? '';
        $email = $_POST['txt_email'] ?? '';
        $celular = $_POST['txt_celular'] ?? '';
        $id_distrito_ubigeo = $_POST['distrito'] ?? '';
        $direccion = $_POST['txt_direccion'] ?? '';
        $estado_civil = $_POST['rdoCIVIL'] ?? '';
        $seguro = $_POST['txt_seguro'] ?? '';
        $fecha_nac = $_POST['date_nac'] ?? '';
        $id_usuario = $_SESSION['user_id']; // Asumiendo un ID de usuario por defecto si no existe
        $estado = $_POST['rd_estado'] ?? 1;
         if (is_null($id_usuario)) {
      // Maneja el error, por ejemplo, redirigiendo a la página de login
      $_SESSION['registration_error'] = "Debe iniciar sesión para realizar esta operación.";
      header("Location: /avance/HTML/PRESENTACION/frmLogin.php");
      exit();
  }

        if (isset($_POST['btn_guardar']) || isset($_POST['btn_editar'])) {
            if (empty($id_distrito_ubigeo)) {
                $errors[] = "Debe seleccionar un departamento, provincia y distrito válidos.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['registration_error'] = implode("<br>", $errors);
        } else {
            $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
            $hashed_password_safe = !is_null($hashed_password) ? "'" . mysqli_real_escape_string($cn, $hashed_password) . "'" : 'NULL';
            $id_usuario_safe = is_null($id_usuario) ? 'NULL' : "'" . mysqli_real_escape_string($cn, $id_usuario) . "'";

            $documento_antiguo_safe = mysqli_real_escape_string($cn, $documento_antiguo);
            $tipo_safe = mysqli_real_escape_string($cn, $tipo);
            $documento_safe = mysqli_real_escape_string($cn, $documento);
            $pat_safe = mysqli_real_escape_string($cn, $pat);
            $mat_safe = mysqli_real_escape_string($cn, $mat);
            $nom_safe = mysqli_real_escape_string($cn, $nom);
            $tipo_contrato_safe = mysqli_real_escape_string($cn, $tipo_contrato);
            $rd_safe = mysqli_real_escape_string($cn, $rd);
            $sexo_safe = mysqli_real_escape_string($cn, $sexo);
            $email_safe = mysqli_real_escape_string($cn, $email);
            $celular_safe = mysqli_real_escape_string($cn, $celular);
            $id_distrito_ubigeo_safe = mysqli_real_escape_string($cn, $id_distrito_ubigeo);
            $direccion_safe = mysqli_real_escape_string($cn, $direccion);
            $estado_civil_safe = mysqli_real_escape_string($cn, $estado_civil);
            $seguro_safe = mysqli_real_escape_string($cn, $seguro);
            $fecha_nac_safe = mysqli_real_escape_string($cn, $fecha_nac);
            $estado_safe = mysqli_real_escape_string($cn, $estado);

            while (mysqli_more_results($cn) && mysqli_next_result($cn));

            $query = "";
            if (isset($_POST['btn_guardar'])) {
                $query = "CALL sp_nuevodocente(
                    $hashed_password_safe, '$tipo_safe', '$documento_safe', 
                    '$tipo_contrato_safe', '$rd_safe', '$pat_safe', '$mat_safe', 
                    '$nom_safe', '$sexo_safe', '$email_safe', '$celular_safe', 
                    '$id_distrito_ubigeo_safe', '$direccion_safe', 
                    '$estado_civil_safe', '$seguro_safe', '$fecha_nac_safe', 
                    $id_usuario_safe
                )";
            } elseif (isset($_POST['btn_editar'])) {
                $query = "CALL sp_actualizadocente(
                    $hashed_password_safe, '$documento_safe', '$tipo_safe', '$tipo_contrato_safe', 
                    '$rd_safe', '$pat_safe', '$mat_safe', '$nom_safe', 
                    '$sexo_safe', '$email_safe', '$celular_safe', 
                    '$id_distrito_ubigeo_safe', '$direccion_safe', 
                    '$estado_civil_safe', '$seguro_safe', '$fecha_nac_safe', $id_usuario_safe,
                    '$estado_safe', '$documento_antiguo_safe'
                )";
            } elseif (isset($_POST['btn_borrar'])) {
                $query = "CALL sp_eliminadocente('$documento_antiguo_safe')";
            }

            if (!empty($query)) {
                $success = mysqli_query($cn, $query);
                if ($success) {
                    $_SESSION['registration_success'] = "¡Operación completada con éxito!";
                } else {
                    $_SESSION['registration_error'] = "Error en la operación: " . mysqli_error($cn);
                }
            }
        }
        header("Location: /avance/HTML/PRESENTACION/frmDocente.php");
        exit();
    }
}