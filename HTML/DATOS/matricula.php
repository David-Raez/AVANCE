<?php
// /xampp/htdocs/avance/HTML/DATOS/matricula.php
session_start();

require_once '../conexion.php';

function redirectToForm($success_message = '', $error_message = '') {
    if ($success_message) {
        $_SESSION['matricula_success'] = $success_message;
    }
    if ($error_message) {
        $_SESSION['matricula_error'] = $error_message;
    }
    header("Location: /avance/HTML/PRESENTACION/frmMatricula.php");
    exit();
}

// Lógica principal para procesar la matrícula (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_matricular'])) {
    
    $n_documento_alumno = $_POST['n_documento_alumno'] ?? '';
    $matricula_tipo = $_POST['matricula_tipo'] ?? '';
    $id_usuario = $_SESSION['user_id'] ?? null;
    $id_carrera = null;
    $id_modulo_ocupacional = null;
    $id_formacion = null;
    $becado = isset($_POST['checkbox_becado']) ? 1 : 0;

    $query_check = "SELECT COUNT(*) FROM matricula WHERE N_DOCUMENTO_ALUMNO = ?";
    $param_value = [$n_documento_alumno];
    $param_type = "s";

    if ($matricula_tipo === 'carrera') {
        $id_carrera = $_POST['select_carrera'] ?? '';
        if (empty($id_carrera)) {
            redirectToForm(null, "Debe seleccionar una carrera.");
        }
        $query_check .= " AND ID_MODULO = ?";
        $param_value[] = $id_carrera;
        $param_type .= "s";
    } else if ($matricula_tipo === 'modulo') {
        $id_modulo_ocupacional = $_POST['select_modulo'] ?? '';
        if (empty($id_modulo_ocupacional)) {
            redirectToForm(null, "Debe seleccionar un módulo.");
        }
        $query_check .= " AND ID_MODULO_OCUPACIONAL = ?";
        $param_value[] = $id_modulo_ocupacional;
        $param_type .= "s";
    } else if ($matricula_tipo === 'formacion') {
        $id_formacion = $_POST['select_formacion'] ?? '';
        if (empty($id_formacion)) {
            redirectToForm(null, "Debe seleccionar una formación continua.");
        }
        $query_check .= " AND ID_FORM = ?";
        $param_value[] = $id_formacion;
        $param_type .= "s";
    } else {
        redirectToForm(null, "Opción de matrícula inválida.");
    }
    
    if (empty($n_documento_alumno)) {
        redirectToForm(null, "Debe seleccionar un alumno.");
    }

    $stmt_check = mysqli_prepare($cn, $query_check);
    if (!$stmt_check) {
        redirectToForm(null, "Error al preparar la consulta de verificación: " . mysqli_error($cn));
    }
    
    mysqli_stmt_bind_param($stmt_check, $param_type, ...$param_value);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);
    
    if ($count > 0) {
        redirectToForm(null, "El alumno ya está matriculado en este programa.");
    }

    $insert_query = "INSERT INTO matricula (N_DOCUMENTO_ALUMNO, ID_USUARIO, ID_MODULO, ID_MODULO_OCUPACIONAL, ID_FORM, BECADO) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($cn, $insert_query);
    
    if ($stmt_insert) {
        $id_carrera = ($matricula_tipo === 'carrera') ? $id_carrera : null;
        $id_modulo_ocupacional = ($matricula_tipo === 'modulo') ? $id_modulo_ocupacional : null;
        $id_formacion = ($matricula_tipo === 'formacion') ? $id_formacion : null;

        // ¡Línea corregida!
        // n_documento_alumno (char), id_usuario (int), id_modulo (char), id_modulo_ocupacional (char), id_form (char), becado (tinyint)
        mysqli_stmt_bind_param($stmt_insert, "sisssi", $n_documento_alumno, $id_usuario, $id_carrera, $id_modulo_ocupacional, $id_formacion, $becado);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            redirectToForm("Matrícula realizada exitosamente.");
        } else {
            redirectToForm(null, "Error al matricular: " . mysqli_error($cn));
        }
    } else {
        redirectToForm(null, "Error al preparar la consulta de inserción.");
    }
    
    mysqli_close($cn);
    exit();
}

// Lógica para BUSCAR alumno (GET)
if (isset($_GET['accion']) && $_GET['accion'] == 'buscar_alumno' && isset($_GET['documento'])) {
    header('Content-Type: application/json');
    $documento = $_GET['documento'];
    
    $query = "SELECT N_DOCUMENTO_ALUMNO, CONCAT(NOMBRES, ' ', APE_PATERNO, ' ', APE_MATERNO) AS NOMBRES FROM alumno WHERE N_DOCUMENTO_ALUMNO = ?";
    $stmt = mysqli_prepare($cn, $query);
    mysqli_stmt_bind_param($stmt, "s", $documento);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $alumno = mysqli_fetch_assoc($result);

    if ($alumno) {
        echo json_encode($alumno);
    } else {
        echo json_encode(['error' => 'Alumno no encontrado.']);
    }
    mysqli_close($cn);
    exit();
}
mysqli_close($cn);
?>