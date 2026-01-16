<?php
// /xampp/htdocs/avance/HTML/DATOS/gestion_matricula.php
session_start();

require_once '../conexion.php';

// Redirecciona a la página de gestión después de la acción
function redirectToGestion($success_message = '', $error_message = '') {
    if ($success_message) {
        $_SESSION['gestion_success'] = $success_message;
    }
    if ($error_message) {
        $_SESSION['gestion_error'] = $error_message;
    }
    header("Location: /avance/HTML/PRESENTACION/frmGestion_matricula.php");
    exit();
}

// Lógica para eliminar una matrícula
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    
    $id_matricula = $_GET['id'];
    
    $delete_query = "DELETE FROM matricula WHERE ID_MATRICULA = ?";
    $stmt = mysqli_prepare($cn, $delete_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_matricula);
        if (mysqli_stmt_execute($stmt)) {
            redirectToGestion("Matrícula eliminada exitosamente.");
        } else {
            redirectToGestion(null, "Error al eliminar la matrícula: " . mysqli_error($cn));
        }
        mysqli_stmt_close($stmt);
    } else {
        redirectToGestion(null, "Error al preparar la consulta de eliminación.");
    }
    
    mysqli_close($cn);
    exit();
}

// Lógica para editar una matrícula (esto es solo un ejemplo)
// Lógica para EDITAR usando STORED PROCEDURE (Nivel Profesional)
if (isset($_POST['accion']) && $_POST['accion'] == 'editar' && isset($_POST['id'])) {
    
    $id_matricula = $_POST['id'];
    $nuevo_dni = $_POST['n_documento_alumno'];
    $tipo = $_POST['tipo_programa'];
    $becado = isset($_POST['becado']) ? 1 : 0;

    // Preparamos variables (Enviamos NULL o string vacío, el SP lo manejará)
    $id_carrera = ($tipo == 'carrera' && !empty($_POST['id_carrera'])) ? $_POST['id_carrera'] : null;
    $id_modulo_oc = ($tipo == 'modulo' && !empty($_POST['id_modulo_oc'])) ? $_POST['id_modulo_oc'] : null;
    $id_formacion = ($tipo == 'formacion' && !empty($_POST['id_formacion'])) ? $_POST['id_formacion'] : null;

    // LLAMAMOS AL PROCEDIMIENTO ALMACENADO
    // La sintaxis es "CALL NombreProcedimiento(?, ?, ...)"
    $query = "CALL SP_EDITAR_MATRICULA(?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($cn, $query);

    if ($stmt) {
        // "sssssi" -> s (string) para los IDs (aunque sean números, viajan seguro como string o null)
        mysqli_stmt_bind_param($stmt, "issssi", 
            $id_matricula, 
            $nuevo_dni, 
            $id_carrera, 
            $id_modulo_oc, 
            $id_formacion, 
            $becado
        );

        if (mysqli_stmt_execute($stmt)) {
            redirectToGestion("Matrícula actualizada correctamente (vía SP).");
        } else {
            redirectToGestion(null, "Error al ejecutar SP: " . mysqli_error($cn));
        }
        mysqli_stmt_close($stmt);
    } else {
        redirectToGestion(null, "Error al preparar el procedimiento.");
    }

    mysqli_close($cn);
    exit();
}
?>
