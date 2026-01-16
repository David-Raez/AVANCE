<?php
// /xampp/htdocs/avance/HTML/DATOS/usuario.php

// Iniciar la sesión al principio del script
session_start();

// Habilitar todos los errores en desarrollo. ¡Cambiar para producción!
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
require_once '../conexion.php';

// Limpiar sesiones anteriores para evitar mostrar datos incorrectos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si la acción es una búsqueda o listado, limpiamos las sesiones de formularios
    if (isset($_POST['btn_buscar']) || isset($_POST['btn_listar']) || isset($_POST['btn_nuevo'])) {
        unset($_SESSION['usuario_encontrado']);
        unset($_SESSION['usuario_data']);
        unset($_SESSION['listado_usuarios']);
        unset($_SESSION['mostrar_form_nuevo']);
        unset($_SESSION['registration_error']);
        unset($_SESSION['registration_success']);
    }
} else {
    // Limpiar todas las sesiones al cargar la página por primera vez
    unset($_SESSION['usuario_encontrado']);
    unset($_SESSION['usuario_data']);
    unset($_SESSION['listado_usuarios']);
    unset($_SESSION['mostrar_form_nuevo']);
    unset($_SESSION['registration_error']);
    unset($_SESSION['registration_success']);
}

// --- Lógica principal solo si la petición es POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Lógica para guardar un nuevo usuario (btn_guardar) ---
    if (isset($_POST['btn_guardar'])) {
        $errors = [];

        // Recoger datos del formulario
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $tipo = $_POST['selTIPO'] ?? '';
        $documento = $_POST['txt_documento'] ?? '';
        $pat = $_POST['txt_paterno'] ?? '';
        $mat = $_POST['txt_materno'] ?? '';
        $nom = $_POST['txt_nombres'] ?? '';
        $id_rol = $_POST['selROL'] ?? '';
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
        $estado_usuario = $_POST['rd_estado'] ?? '1'; // Por defecto, activo

        // Validaciones
        if ($password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }
        if (strlen($password) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres.";
        }
        if (empty($documento)) {
            $errors[] = "El número de documento es obligatorio.";
        }
        if (empty($id_distrito_ubigeo)) {
            $errors[] = "Debe seleccionar un distrito.";
        }

        // Validación para documento duplicado con un SP
        if (empty($errors)) {
            $stmt_check = mysqli_prepare($cn, "CALL sp_ValidarDocumentoUsuario(?)");
            if ($stmt_check) {
                mysqli_stmt_bind_param($stmt_check, "s", $documento);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $row_check = mysqli_fetch_assoc($result_check);
                mysqli_stmt_close($stmt_check);
                
                while(mysqli_more_results($cn) && mysqli_next_result($cn));
                
                if ($row_check && $row_check['count'] > 0) {
                    $errors[] = "El número de documento ya está registrado.";
                }
            } else {
                $errors[] = "Error al preparar la validación del documento.";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['registration_error'] = implode(" ", $errors);
            $_SESSION['mostrar_form_nuevo'] = true;
            $_SESSION['usuario_data'] = $_POST;
            header("Location: ../PRESENTACION/frmUsuario.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "CALL sp_nuevousuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($cn, $sql);
        
        if ($stmt) {
            $id_rol_int = (int)$id_rol;
            $id_distrito_int = (int)$id_distrito_ubigeo;
            $estado_usuario_int = (int)$estado_usuario;
            
            mysqli_stmt_bind_param(
                $stmt, "sssssssssississsi",
                $hashed_password, $tipo, $documento, $pat, $mat, $nom, $tipo_contrato, $rd, $sexo, $email, 
                $celular, $id_rol_int, $id_distrito_int, $direccion, $estado_civil, $seguro, $fecha_nac, 
                $estado_usuario_int
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['registration_success'] = "Registro de usuario guardado con éxito!";
            } else {
                $_SESSION['registration_error'] = "Error al guardar el registro (SP): " . mysqli_stmt_error($stmt);
                error_log("Error de inserción (SP): " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['registration_error'] = "Error en la preparación de la consulta (SP): " . mysqli_error($cn);
            error_log("Error al preparar la consulta (SP): " . mysqli_error($cn));
        }
        
        while(mysqli_more_results($cn) && mysqli_next_result($cn));
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
    
    // --- Lógica para buscar un usuario (btn_buscar) ---
    if (isset($_POST['btn_buscar'])) {
        $valor = $_POST['txt_valor'] ?? '';
        $tipo_busqueda = $_POST['selTipo_busqueda'] ?? '1';

        if (empty($valor)) {
            $_SESSION['registration_error'] = "No hay datos a buscar.";
            header("Location: ../PRESENTACION/frmUsuario.php");
            exit;
        }

        if ($tipo_busqueda === '1') { 
            $query = "CALL sp_buscausuario_pordni(?)";
            $stmt = mysqli_prepare($cn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $valor);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $usuario_data = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
                
                if ($usuario_data) {
                    $_SESSION['usuario_encontrado'] = true;
                    $_SESSION['usuario_data'] = $usuario_data;
                } else {
                    $_SESSION['registration_error'] = "No se encontró ningún usuario con ese documento.";
                }
                mysqli_stmt_close($stmt);
                while(mysqli_more_results($cn) && mysqli_next_result($cn));
            } else {
                $_SESSION['registration_error'] = "Error al preparar la búsqueda (SP): " . mysqli_error($cn);
            }
        } else { 
            $valor_like = "%" . $valor . "%";
            $query = "CALL sp_buscausuario_por_nombre(?)";
            $stmt = mysqli_prepare($cn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $valor_like);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $listado_usuarios = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $listado_usuarios[] = $row;
                }
                mysqli_free_result($result);
                mysqli_stmt_close($stmt);
                
                while(mysqli_more_results($cn) && mysqli_next_result($cn));
                
                if (count($listado_usuarios) === 1) {
                    $_SESSION['usuario_encontrado'] = true;
                    $_SESSION['usuario_data'] = $listado_usuarios[0];
                } elseif (count($listado_usuarios) > 1) {
                    $_SESSION['listado_usuarios'] = $listado_usuarios;
                } else {
                    $_SESSION['registration_error'] = "No se encontraron usuarios que coincidan con la búsqueda.";
                }
            } else {
                $_SESSION['registration_error'] = "Error al preparar la búsqueda por nombre (SP): " . mysqli_error($cn);
            }
        }
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
    
    // --- Lógica para mostrar el formulario de nuevo registro (btn_nuevo) ---
    if (isset($_POST['btn_nuevo'])) {
        $_SESSION['mostrar_form_nuevo'] = true;
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
    
    // --- Lógica para listar todos los usuarios (btn_listar) ---
    if (isset($_POST['btn_listar'])) {
        $query = "CALL sp_listadousuarios()";
        $result = mysqli_query($cn, $query);
        if ($result) {
            $listado = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $listado[] = $row;
            }
            mysqli_free_result($result);
            $_SESSION['listado_usuarios'] = $listado;
        } else {
            $_SESSION['registration_error'] = "Error al cargar la tabla de usuarios (SP): " . mysqli_error($cn);
        }
        while(mysqli_more_results($cn) && mysqli_next_result($cn));
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
    
    // --- Lógica para editar desde el listado (btn_editar_seleccionado) ---
    if (isset($_POST['btn_editar_seleccionado'])) {
        $documento = $_POST['rd_elegir'] ?? '';
        if (empty($documento)) {
            $_SESSION['registration_error'] = "Debe seleccionar un usuario para editar.";
            header("Location: ../PRESENTACION/frmUsuario.php");
            exit;
        }
        
        $query = "CALL sp_buscausuario_pordni(?)";
        $stmt = mysqli_prepare($cn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $documento);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $usuario_data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            if ($usuario_data) {
                $_SESSION['usuario_encontrado'] = true;
                $_SESSION['usuario_data'] = $usuario_data;
            } else {
                $_SESSION['registration_error'] = "Usuario no encontrado.";
            }
            mysqli_stmt_close($stmt);
            while(mysqli_more_results($cn) && mysqli_next_result($cn));
        } else {
            $_SESSION['registration_error'] = "Error al preparar la búsqueda (SP): " . mysqli_error($cn);
        }
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit;
    }
    
    // --- Lógica para borrar desde el listado o formulario (btn_borrar, btn_eliminar_seleccionado) ---
    if (isset($_POST['btn_borrar']) || isset($_POST['btn_eliminar_seleccionado'])) {
        $documento_borrar = $_POST['txt_documento'] ?? $_POST['rd_elegir'] ?? '';
        if (empty($documento_borrar)) {
            $_SESSION['registration_error'] = "Debe seleccionar un usuario para borrar.";
        } else {
            $stmt_delete = mysqli_prepare($cn, "CALL sp_borrausuario(?)");
            if ($stmt_delete) {
                mysqli_stmt_bind_param($stmt_delete, "s", $documento_borrar);
                if (mysqli_stmt_execute($stmt_delete)) {
                    $_SESSION['registration_success'] = "Usuario borrado con éxito.";
                } else {
                    $_SESSION['registration_error'] = "Error al borrar el usuario (SP): " . mysqli_stmt_error($stmt_delete);
                }
                mysqli_stmt_close($stmt_delete);
                while(mysqli_more_results($cn) && mysqli_next_result($cn));
            } else {
                $_SESSION['registration_error'] = "Error al preparar la eliminación (SP): " . mysqli_error($cn);
            }
        }
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
    
    // --- Lógica para actualizar un usuario (btn_editar) ---
    if (isset($_POST['btn_editar'])) {
        $id_usuario = $_POST['id_usuario'] ?? '';
        $documento_antiguo = $_POST['txt_documento_antiguo'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
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
        $id_rol = $_POST['selROL'] ?? '';
        $id_distrito_ubigeo = $_POST['distrito'] ?? '';
        $direccion = $_POST['txt_direccion'] ?? '';
        $estado_civil = $_POST['rdoCIVIL'] ?? '';
        $seguro = $_POST['txt_seguro'] ?? '';
        $fecha_nac = $_POST['date_nac'] ?? '';
        $estado_usuario = $_POST['rd_estado'] ?? '';

        $errors = [];
        
        if ($documento !== $documento_antiguo) {
            $stmt_check = mysqli_prepare($cn, "SELECT ID_USUARIO FROM usuario WHERE N_DOCUMENTO_USUARIO = ? AND ID_USUARIO != ? LIMIT 1");
            if ($stmt_check) {
                mysqli_stmt_bind_param($stmt_check, "si", $documento, $id_usuario);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $errors[] = "El nuevo número de documento ya está registrado.";
                }
                mysqli_stmt_close($stmt_check);
                while(mysqli_more_results($cn) && mysqli_next_result($cn));
            }
        }
        
        if (!empty($password) || !empty($confirm_password)) {
            if ($password !== $confirm_password) {
                $errors[] = "Las contraseñas no coinciden.";
            }
            if (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['registration_error'] = implode(" ", $errors);
            $_SESSION['usuario_encontrado'] = true;
            $_SESSION['usuario_data'] = $_POST;
            header("Location: ../PRESENTACION/frmUsuario.php");
            exit();
        }
        
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_update = "CALL sp_actualizausuario_con_password(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_update = mysqli_prepare($cn, $sql_update);
            
            if ($stmt_update) {
                $id_usuario_int = (int)$id_usuario;
                $id_rol_int = (int)$id_rol;
                $id_distrito_ubigeo_int = (int)$id_distrito_ubigeo;
                $estado_usuario_int = (int)$estado_usuario;
                
                mysqli_stmt_bind_param($stmt_update, "issssssisssissssisi", 
                    $id_usuario_int, $tipo, $documento, $pat, $mat, $nom, $tipo_contrato, $rd, $sexo, $email, 
                    $celular, $id_rol_int, $id_distrito_ubigeo_int, $direccion, $estado_civil, $seguro, $fecha_nac, 
                    $estado_usuario_int, $hashed_password
                );
            }
        } else {
            $sql_update = "CALL sp_actualizausuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_update = mysqli_prepare($cn, $sql_update);
            
            if ($stmt_update) {
                $id_usuario_int = (int)$id_usuario;
                $id_rol_int = (int)$id_rol;
                $id_distrito_ubigeo_int = (int)$id_distrito_ubigeo;
                $estado_usuario_int = (int)$estado_usuario;
                
                mysqli_stmt_bind_param($stmt_update, "issssssisssisssisi", 
                    $id_usuario_int, $tipo, $documento, $pat, $mat, $nom, $tipo_contrato, $rd, $sexo, $email, 
                    $celular, $id_rol_int, $id_distrito_ubigeo_int, $direccion, $estado_civil, $seguro, $fecha_nac, 
                    $estado_usuario_int
                );
            }
        }
        
        if ($stmt_update) {
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['registration_success'] = "Usuario actualizado con éxito!";
            } else {
                $_SESSION['registration_error'] = "Error al actualizar el usuario (SP): " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
            while(mysqli_more_results($cn) && mysqli_next_result($cn));
        } else {
            $_SESSION['registration_error'] = "Error en la preparación de la actualización (SP): " . mysqli_error($cn);
        }
        header("Location: ../PRESENTACION/frmUsuario.php");
        exit();
    }
}
if (isset($cn) && is_object($cn)) {
    mysqli_close($cn);
}
?>