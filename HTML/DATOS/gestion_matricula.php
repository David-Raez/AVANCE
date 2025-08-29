<?php
// /xampp/htdocs/avance/HTML/DATOS/gestion_matricula.php
session_start();

require_once '/xampp/htdocs/avance/HTML/conexion.php';

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
if (isset($_POST['accion']) && $_POST['accion'] == 'editar' && isset($_POST['id'])) {
    // Aquí iría el código para procesar la edición de la matrícula.
    // Necesitarías recibir los datos del formulario de edición y
    // ejecutar una consulta UPDATE.
    
    // Ejemplo de un mensaje de éxito/error al finalizar la edición:
    // redirectToGestion("Matrícula actualizada exitosamente.");
    // o
    // redirectToGestion(null, "Error al actualizar la matrícula.");

    mysqli_close($cn);
    exit();
}
?>
