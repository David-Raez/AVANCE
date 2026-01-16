<?php
// /xampp/htdocs/avance/HTML/DATOS/modulo_ocupacional.php
session_start();

require_once '../conexion.php';

// Maneja las peticiones POST (Guardar, Actualizar, Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACCIÓN: ELIMINAR MÓDULO ---
    if (isset($_POST['btn_eliminar'])) {
        $id_modulo_eliminar = $_POST['id_modulo_eliminar'];
        // Llamamos al procedimiento para eliminar
        $query = "CALL SP_EliminarModuloOcupacional(?)";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_modulo_eliminar);
        if (mysqli_stmt_execute($stmt)) {
            // Envía un código de estado 200 (OK) al script de JS
            http_response_code(200);
            exit();
        } else {
            // Envía un código de estado 500 (Error) y el mensaje de error
            http_response_code(500);
            echo "Error al eliminar el módulo: " . mysqli_error($cn);
            exit();
        }
    }
    
    // --- ACCIÓN: GUARDAR/ACTUALIZAR MÓDULO ---
    if (isset($_POST['btn_guardar_modulo'])) {
        $id_docente = $_POST['id_docente'];
        $id_modulo = $_POST['txt_id_modulo'];
        $nombre = $_POST['txt_nombre'];
        $turno = $_POST['txt_turno'];
        $ciclo = $_POST['txt_ciclo'];
        $duracion = $_POST['txt_duracion'];
        $opcion_ocupacional = $_POST['txt_opcion_ocupacional'];
        $familia_profesional = $_POST['txt_familia_profesional'];
        $resolucion_directorial = $_POST['txt_resolucion_directorial'];
        $inicio = $_POST['txt_inicio'];
        $fin = $_POST['txt_fin'];
        $id_modulo_oculto = $_POST['id_modulo_oculto'] ?? '';

        if (!empty($id_modulo_oculto)) {
            // Es una actualización, llamamos al procedimiento de actualizar
            $query = "CALL SP_ActualizarModuloOcupacional(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sisssssssss", $id_modulo, $id_docente, $nombre, $turno, $ciclo, $duracion, $opcion_ocupacional, $familia_profesional, $resolucion_directorial, $inicio, $fin);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['modulo_success'] = "Módulo actualizado exitosamente.";
            } else {
                $_SESSION['modulo_error'] = "Error al actualizar el módulo: " . mysqli_error($cn);
            }
        } else {
            // Es una inserción, llamamos al procedimiento de insertar
            $query = "CALL SP_InsertarModuloOcupacional(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sisssssssss", $id_modulo, $id_docente, $nombre, $turno, $ciclo, $duracion, $opcion_ocupacional, $familia_profesional, $resolucion_directorial, $inicio, $fin);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['modulo_success'] = "Módulo registrado exitosamente.";
            } else {
                $_SESSION['modulo_error'] = "Error al registrar el módulo: " . mysqli_error($cn);
            }
        }
        // Redireccionar al formulario principal después de guardar/actualizar
        header("Location: /avance/HTML/PRESENTACION/frmModulo_ocupacional.php");
        exit();
    }
}
mysqli_close($cn);
?>