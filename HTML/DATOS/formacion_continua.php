<?php
// /xampp/htdocs/avance/HTML/DATOS/formacion_continua.php
session_start();

require_once '/xampp/htdocs/avance/HTML/conexion.php';

// Maneja las peticiones POST (Guardar, Actualizar, Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACCIÓN: ELIMINAR FORMACIÓN ---
    if (isset($_POST['btn_eliminar'])) {
        $id_formacion_eliminar = $_POST['id_formacion_eliminar'];
        // Llamamos al procedimiento para eliminar
        $query = "CALL SP_EliminarFormacionContinua(?)";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_formacion_eliminar);
        if (mysqli_stmt_execute($stmt)) {
            // Envía un código de estado 200 (OK) al script de JS
            http_response_code(200);
            exit();
        } else {
            // Envía un código de estado 500 (Error) y el mensaje de error
            http_response_code(500);
            echo "Error al eliminar la formación: " . mysqli_error($cn);
            exit();
        }
    }
    
    // --- ACCIÓN: GUARDAR/ACTUALIZAR FORMACIÓN ---
    if (isset($_POST['btn_guardar_formacion'])) {
        $id_form = $_POST['txt_id_form'];
        $id_docente = $_POST['id_docente'];
        $familia_productiva = $_POST['txt_familia_productiva'];
        $nombre_formacion = $_POST['txt_nombre_formacion'];
        $modulo_unidad = $_POST['txt_modulo_unidad'];
        $modalidad = $_POST['txt_modalidad'];
        $creditos = $_POST['txt_creditos'];
        $horas = $_POST['txt_horas'];
        $inicio = $_POST['txt_inicio'];
        $fin = $_POST['txt_fin'];
        $estado = $_POST['txt_estado'];
        $id_form_oculto = $_POST['id_form_oculto'] ?? '';

        if (!empty($id_form_oculto)) {
            // Es una actualización
            $query = "CALL SP_ActualizarFormacionContinua(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sssssiissss", $id_form, $id_docente, $familia_productiva, $nombre_formacion, $modulo_unidad, $modalidad, $creditos, $horas, $inicio, $fin, $estado);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['fc_success'] = "Formación continua actualizada exitosamente.";
            } else {
                $_SESSION['fc_error'] = "Error al actualizar la formación continua: " . mysqli_error($cn);
            }
        } else {
            // Es una inserción
            $query = "CALL SP_InsertarFormacionContinua(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sissssiisss", $id_form, $id_docente, $familia_productiva, $nombre_formacion, $modulo_unidad, $modalidad, $creditos, $horas, $inicio, $fin, $estado);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['fc_success'] = "Formación continua registrada exitosamente.";
            } else {
                $_SESSION['fc_error'] = "Error al registrar la formación continua: " . mysqli_error($cn);
            }
        }
        // Redireccionar al formulario principal después de guardar/actualizar
        header("Location: /avance/HTML/PRESENTACION/frmFormacion.php");
        exit();
    }
}
mysqli_close($cn);
?>