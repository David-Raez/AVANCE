<?php
// /xampp/htdocs/avance/HTML/DATOS/unidad_didactica.php
session_start();

require_once '../conexion.php';

// Redireccionar al formulario principal
function redirectToForm($success_message = '', $error_message = '') {
    if ($success_message) {
        $_SESSION['unidad_success'] = $success_message;
    }
    if ($error_message) {
        $_SESSION['unidad_error'] = $error_message;
    }
    header("Location: /avance/HTML/PRESENTACION/frmUnidad_didactica.php");
    exit();
}

// ------------------- LÓGICA DE PROCESAMIENTO -------------------

// --- ACCIÓN: LISTAR UNIDADES (Llamada desde JavaScript) ---
if (isset($_GET['accion']) && $_GET['accion'] == 'listar' && isset($_GET['id_modulo'])) {
    header('Content-Type: application/json');
    $id_modulo = $_GET['id_modulo'];
    $unidades = [];
    try {
        $query = "SELECT ID_UNIDAD, NOMBRE_UNIDAD, CLASES, CREDITO, HORAS, INICIO, FIN FROM unidad_didactica WHERE ID_MODULO = ? ORDER BY ID_UNIDAD";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_modulo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $unidades[] = $row;
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error en la base de datos.']);
        exit();
    }
    echo json_encode($unidades);
    mysqli_close($cn);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- ACCIÓN: GUARDAR/ACTUALIZAR UNIDAD ---
    if (isset($_POST['btn_guardar_unidad'])) {
        $id_modulo = $_POST['id_modulo_oculto'];
        $id_unidad = $_POST['txt_id_unidad'];
        $nombre = $_POST['txt_nombre'];
        $clases = $_POST['num_clases'];
        $credito = $_POST['num_credito'];
        $horas = $_POST['num_horas'];
        $inicio = $_POST['txt_inicio'];
        $fin = $_POST['txt_fin'];
        $id_unidad_oculto = $_POST['id_unidad_oculto'] ?? '';

        if (!empty($id_unidad_oculto)) {
            // Es una actualización (UPDATE)
            $query = "UPDATE unidad_didactica SET NOMBRE_UNIDAD = ?, CLASES = ?, CREDITO = ?, HORAS = ?, INICIO = ?, FIN = ? WHERE ID_UNIDAD = ? AND ID_MODULO = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "siiissss", $nombre, $clases, $credito, $horas, $inicio, $fin, $id_unidad, $id_modulo);
            if (mysqli_stmt_execute($stmt)) {
                redirectToForm("Unidad didáctica actualizada exitosamente.");
            } else {
                redirectToForm(null, "Error al actualizar la unidad: " . mysqli_error($cn));
            }
        } else {
            // Es una inserción (INSERT)
            // Validar si el ID_UNIDAD ya existe para este módulo
            $query_check = "SELECT COUNT(*) FROM unidad_didactica WHERE ID_UNIDAD = ? AND ID_MODULO = ?";
            $stmt_check = mysqli_prepare($cn, $query_check);
            mysqli_stmt_bind_param($stmt_check, "ss", $id_unidad, $id_modulo);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_bind_result($stmt_check, $count);
            mysqli_stmt_fetch($stmt_check);
            mysqli_stmt_close($stmt_check);
            
            if ($count > 0) {
                redirectToForm(null, "El ID de unidad didáctica ya existe en este módulo.");
            } else {
                $query = "INSERT INTO unidad_didactica (ID_UNIDAD, ID_MODULO, NOMBRE_UNIDAD, CLASES, CREDITO, HORAS, INICIO, FIN) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($cn, $query);
                mysqli_stmt_bind_param($stmt, "sssiiiss", $id_unidad, $id_modulo, $nombre, $clases, $credito, $horas, $inicio, $fin);
                if (mysqli_stmt_execute($stmt)) {
                    redirectToForm("Unidad didáctica registrada exitosamente.");
                } else {
                    redirectToForm(null, "Error al registrar la unidad: " . mysqli_error($cn));
                }
            }
        }
    }
    
    // --- ACCIÓN: ELIMINAR UNIDAD ---
    if (isset($_POST['btn_eliminar_unidad'])) {
        $id_unidad = $_POST['id_unidad'];
        try {
            $query = "DELETE FROM unidad_didactica WHERE ID_UNIDAD = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "s", $id_unidad);
            if (mysqli_stmt_execute($stmt)) {
                echo "Unidad didáctica eliminada exitosamente.";
            } else {
                echo "Error al eliminar la unidad. Puede tener dependencias.";
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al eliminar la unidad.";
        }
        mysqli_close($cn);
        exit();
    }
}

mysqli_close($cn);
?>