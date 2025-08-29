<?php
session_start();
require_once '/xampp/htdocs/avance/HTML/conexion.php';

// Mostrar errores en desarrollo. ¡Desactivar para producción!
ini_set('display_errors', 1);

error_reporting(E_ALL);

// Limpiar todas las variables de sesión para evitar mostrar datos antiguos
function limpiar_sesion() {
    unset($_SESSION['alumno_encontrado']);
    unset($_SESSION['alumno_data']);
    unset($_SESSION['listado_alumnos']);
    unset($_SESSION['mostrar_form_nuevo']);
}

// Lógica para procesar las peticiones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- LÓGICA DE BÚSQUEDA ---
    if (isset($_POST['btn_buscar'])) {
        limpiar_sesion();
        $tipo_busqueda = $_POST['selTipo_busqueda'] ?? '';
        $valor = $_POST['txt_valor'] ?? '';

        if (empty($valor)) {
            $_SESSION['registration_error'] = "No hay datos a buscar.";
        } else {
            while (mysqli_more_results($cn) && mysqli_next_result($cn));
            
            $query = ($tipo_busqueda == '0') ? "CALL sp_buscaalumno_por_nombre(?)" : "CALL sp_buscaalumno_pordni(?)";
            
            $stmt = mysqli_prepare($cn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $valor);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $alumnos = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $alumnos[] = $row;
                    }
                    if (count($alumnos) == 1) {
                        $_SESSION['alumno_encontrado'] = true;
                        $_SESSION['alumno_data'] = $alumnos[0];
                        $_SESSION['registration_success'] = "Alumno encontrado exitosamente.";
                    } else {
                        $_SESSION['listado_alumnos'] = $alumnos;
                        $_SESSION['registration_success'] = "Se encontraron " . count($alumnos) . " alumnos. Seleccione uno para editar o borrar.";
                    }
                } else {
                    $_SESSION['registration_error'] = "El alumno no existe o el valor ingresado es incorrecto.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['registration_error'] = "Error al preparar la búsqueda: " . mysqli_error($cn);
            }
        }
        header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
        exit();
    }
    
    // --- LÓGICA DE LISTAR ---
    if (isset($_POST['btn_listar'])) {
        limpiar_sesion();

        while (mysqli_more_results($cn) && mysqli_next_result($cn));
        $rs = mysqli_query($cn, "CALL sp_listadoalumno");
        if ($rs) {
            $listado_alumnos = [];
            while ($row = mysqli_fetch_assoc($rs)) {
                $listado_alumnos[] = $row;
            }
            mysqli_free_result($rs);
            $_SESSION['listado_alumnos'] = $listado_alumnos;
            $_SESSION['registration_success'] = "Listado de alumnos cargado.";
        } else {
            $_SESSION['registration_error'] = "Error al cargar la tabla de alumnos: " . mysqli_error($cn);
        }
        header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
        exit();
    }
    
    // --- LÓGICA DE NUEVO REGISTRO ---
    if (isset($_POST['btn_nuevo'])) {
        limpiar_sesion();
        $_SESSION['mostrar_form_nuevo'] = true;
        
        header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
        exit();
    }

    // --- LÓGICA PARA SELECCIONAR (desde el listado) ---
    if (isset($_POST['btn_seleccionar_editar'])) {
        limpiar_sesion();
        $documento_selec = $_POST['rd_elegir'] ?? '';

        while (mysqli_more_results($cn) && mysqli_next_result($cn));
        $query = "CALL sp_selecciona_alumno_por_doc(?)";
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
        
        limpiar_sesion();
        
        $errors = [];
        $query = "";
        $query_type = '';

        if (isset($_POST['btn_guardar'])) {
            $query_type = 'guardar';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            if (empty($password) || $password !== $confirm_password) {
                $errors[] = "Las contraseñas no coinciden o están vacías.";
            } elseif (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
        } elseif (isset($_POST['btn_editar'])) {
            $query_type = 'editar';
            $password = $_POST['password'] ?? null;
            $confirm_password = $_POST['confirm_password'] ?? null;
            if (!empty($password) && $password !== $confirm_password) {
                $errors[] = "Las contraseñas no coinciden.";
            } elseif (!empty($password) && strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
        } elseif (isset($_POST['btn_borrar'])) {
            $query_type = 'borrar';
            $documento_antiguo = $_POST['txt_documento_antiguo'] ?? '';
        }

        // Obtener y validar datos del formulario
        if (isset($_POST['btn_guardar']) || isset($_POST['btn_editar'])) {
            $documento_antiguo = $_POST['txt_documento_antiguo'] ?? null;
            $tipo = $_POST['selTIPO'] ?? '';
            $documento = $_POST['txt_documento'] ?? '';
            $paterno = $_POST['txt_paterno'] ?? '';
            $materno = $_POST['txt_materno'] ?? '';
            $nombres = $_POST['txt_nombres'] ?? '';
            $estado_civil = $_POST['rdoCIVIL'] ?? '';
            $grado_instruccion = $_POST['rd_grado_instruccion'] ?? '';
            $sexo = $_POST['rdoSEXO'] ?? '';
            $email = $_POST['txt_email'] ?? '';
            $celular = $_POST['txt_celular'] ?? '';
            $conadis = $_POST['rdoCONADIS'] ?? '';
            $n_conadis = $_POST['txt_n_conadis'] ?? '';
            $rd_conadis = $_POST['txt_rd_conadis'] ?? '';
            $seguro = $_POST['txt_seguro'] ?? '';
            $ocupacion = $_POST['txt_ocupacion'] ?? '';
            $distrito_ubigeo = $_POST['distrito_ubigeo'] ?? '';
            $direccion = $_POST['txt_direccion'] ?? '';
            $pais_nac = $_POST['txt_pais_nac'] ?? '';
            $depar_nac = $_POST['txt_depar_nac'] ?? '';
            $provin_nac = $_POST['txt_provin_nac'] ?? '';
            $distrito_nac = $_POST['txt_distrito_nac'] ?? '';
            $lugar_nac = $_POST['txt_lugar_nac'] ?? '';
            $estado = $_POST['rd_estado'] ?? 1;
            $fecha_nac = $_POST['date_nac'] ?? '';
            $id_usuario = $_SESSION['user_id'] ?? null;

            if (is_null($id_usuario)) {
                $_SESSION['registration_error'] = "Debe iniciar sesión para realizar esta operación.";
                header("Location: /avance/HTML/PRESENTACION/frmLogin.php");
                exit();
            }

            if (empty($distrito_ubigeo)) {
                $errors[] = "Debe seleccionar un departamento, provincia y distrito válidos.";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['registration_error'] = implode("<br>", $errors);
            header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
            exit();
        }

        // --- LÓGICA PARA GUARDAR O EDITAR ---
        if ($query_type === 'guardar' || $query_type === 'editar') {
            $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
            
            $stmt = null;
            if ($query_type === 'guardar') {
                while (mysqli_more_results($cn) && mysqli_next_result($cn));
                $stmt = mysqli_prepare($cn, "CALL sp_nuevoalumno(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param(
                    $stmt, "ssssssssssssssssssssssssss", 
                    $hashed_password, $tipo, $documento, $paterno, $materno, $nombres, $grado_instruccion, 
                    $sexo, $email, $celular, $distrito_ubigeo, $direccion, $ocupacion, $estado_civil, 
                    $conadis, $n_conadis, $rd_conadis, $seguro, $pais_nac, $depar_nac, $provin_nac, 
                    $distrito_nac, $lugar_nac, $fecha_nac, $estado, $id_usuario
                );
            } elseif ($query_type === 'editar') {
                while (mysqli_more_results($cn) && mysqli_next_result($cn));
                $stmt = mysqli_prepare($cn, "CALL sp_actualizaalumno(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param(
                    $stmt, "sssssssssssssssssssssssssss", 
                    $documento_antiguo, $hashed_password, $tipo, $documento, $paterno, $materno, $nombres, 
                    $grado_instruccion, $sexo, $email, $celular, $distrito_ubigeo, $direccion, $ocupacion, 
                    $estado_civil, $conadis, $n_conadis, $rd_conadis, $seguro, $pais_nac, $depar_nac, 
                    $provin_nac, $distrito_nac, $lugar_nac, $fecha_nac, $estado, $id_usuario
                );
            }

            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $filas_afectadas = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($filas_afectadas > 0) {
                    $_SESSION['registration_success'] = "¡Operación completada con éxito!";
                } else {
                    $_SESSION['registration_error'] = "Error en la operación o no se realizó ningún cambio.";
                }
            } else {
                $_SESSION['registration_error'] = "Error al preparar la consulta: " . mysqli_error($cn);
            }
        }
        
        // --- LÓGICA PARA ELIMINAR ---
        if ($query_type === 'borrar') {
            while (mysqli_more_results($cn) && mysqli_next_result($cn));
            $stmt = mysqli_prepare($cn, "CALL sp_eliminaalumno(?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $documento_antiguo);
                mysqli_stmt_execute($stmt);
                $filas_afectadas = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($filas_afectadas > 0) {
                    $_SESSION['registration_success'] = "Alumno eliminado correctamente.";
                } else {
                    $_SESSION['registration_error'] = "Error al eliminar el alumno o no fue encontrado.";
                }
            } else {
                $_SESSION['registration_error'] = "Error al preparar la consulta de eliminación: " . mysqli_error($cn);
            }
        }
        header("Location: /avance/HTML/PRESENTACION/frmAlumno.php");
        exit();
    }
}
?>