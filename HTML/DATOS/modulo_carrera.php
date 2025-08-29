<?php
// /xampp/htdocs/avance/HTML/DATOS/modulo_carrera.php
session_start();

require_once '/xampp/htdocs/avance/HTML/conexion.php';

// Redireccionar al formulario principal
function redirectToForm($success_message = '', $error_message = '') {
    if ($success_message) {
        $_SESSION['modulo_success'] = $success_message;
    }
    if ($error_message) {
        $_SESSION['modulo_error'] = $error_message;
    }
    header("Location: /avance/HTML/PRESENTACION/frmModulo_carrera.php");
    exit();
}

// ------------------- LÓGICA DE PROCESAMIENTO -------------------

// --- ACCIÓN: LISTAR MÓDULOS (Llamada desde JavaScript) ---
if (isset($_GET['accion']) && $_GET['accion'] == 'listar' && isset($_GET['id_carrera'])) {
    header('Content-Type: application/json');
    $id_carrera = $_GET['id_carrera'];
    $modulos = [];
    try {
        $query = "SELECT ID_MODULO, NOMBRE, DURACION, INICIO, FIN FROM modulo WHERE ID_CARRERA = ? ORDER BY ID_MODULO";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_carrera);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $modulos[] = $row;
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error en la base de datos.']);
        exit();
    }
    echo json_encode($modulos);
    mysqli_close($cn);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- ACCIÓN: GUARDAR/ACTUALIZAR MÓDULO ---
    if (isset($_POST['btn_guardar_modulo'])) {
        $id_carrera = $_POST['id_carrera_oculto'];
        $id_modulo = $_POST['txt_id_modulo'];
        $nombre = $_POST['txt_nombre'];
        $duracion = $_POST['txt_duracion'];
        $inicio = $_POST['txt_inicio'];
        $fin = $_POST['txt_fin'];
        $id_modulo_oculto = $_POST['id_modulo_oculto'] ?? '';

        if (!empty($id_modulo_oculto)) {
            // Es una actualización (UPDATE)
            $query = "UPDATE modulo SET NOMBRE = ?, DURACION = ?, INICIO = ?, FIN = ? WHERE ID_MODULO = ? AND ID_CARRERA = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $nombre, $duracion, $inicio, $fin, $id_modulo, $id_carrera);
            if (mysqli_stmt_execute($stmt)) {
                redirectToForm("Módulo actualizado exitosamente.");
            } else {
                redirectToForm(null, "Error al actualizar el módulo: " . mysqli_error($cn));
            }
        } else {
            // Es una inserción (INSERT)
            // Validar si el ID_MODULO ya existe para esta carrera
            $query_check = "SELECT COUNT(*) FROM modulo WHERE ID_MODULO = ? AND ID_CARRERA = ?";
            $stmt_check = mysqli_prepare($cn, $query_check);
            mysqli_stmt_bind_param($stmt_check, "si", $id_modulo, $id_carrera);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_bind_result($stmt_check, $count);
            mysqli_stmt_fetch($stmt_check);
            mysqli_stmt_close($stmt_check);
            
            if ($count > 0) {
                redirectToForm(null, "El ID de módulo ya existe en esta carrera.");
            } else {
                $query = "INSERT INTO modulo (ID_MODULO, ID_CARRERA, NOMBRE, DURACION, INICIO, FIN) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($cn, $query);
                mysqli_stmt_bind_param($stmt, "sissss", $id_modulo, $id_carrera, $nombre, $duracion, $inicio, $fin);
                if (mysqli_stmt_execute($stmt)) {
                    redirectToForm("Módulo registrado exitosamente.");
                } else {
                    redirectToForm(null, "Error al registrar el módulo: " . mysqli_error($cn));
                }
            }
        }
    }
    
    // --- ACCIÓN: ELIMINAR MÓDULO ---
    if (isset($_POST['btn_eliminar_modulo'])) {
        $id_modulo = $_POST['id_modulo'];
        try {
            $query = "DELETE FROM modulo WHERE ID_MODULO = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "s", $id_modulo);
            if (mysqli_stmt_execute($stmt)) {
                echo "Módulo eliminado exitosamente.";
            } else {
                echo "Error al eliminar el módulo. Asegúrese de que no tenga unidades didácticas asociadas.";
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al eliminar el módulo.";
        }
        mysqli_close($cn);
        exit();
    }
}

mysqli_close($cn);
?>