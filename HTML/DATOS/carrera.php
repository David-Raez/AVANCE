<?php
// /xampp/htdocs/avance/HTML/DATOS/carrera.php
session_start();

require_once '../conexion.php';

// Redireccionar al formulario principal
function redirectToForm() {
    header("Location: /avance/HTML/PRESENTACION/frmCarrera.php");
    exit();
}

// ------------------- LÓGICA DE PROCESAMIENTO -------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACCIÓN: BUSCAR UNA CARRERA ---
    if (isset($_POST['btn_buscar'])) {
        $programa_estudio = $_POST['txt_buscar'] ?? '';
        
        if (!empty($programa_estudio)) {
            $query = "SELECT * FROM carrera WHERE PROGRAMA_ESTUDIO LIKE ?";
            $stmt = mysqli_prepare($cn, $query);
            $search_term = "%" . $programa_estudio . "%";
            mysqli_stmt_bind_param($stmt, "s", $search_term);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $carrera = mysqli_fetch_assoc($result);

            if ($carrera) {
                $_SESSION['carrera_encontrada'] = true;
                $_SESSION['carrera_data'] = $carrera;
            } else {
                $_SESSION['carrera_error'] = "Carrera no encontrada.";
            }
        }
        redirectToForm();
    }

    // --- ACCIÓN: NUEVO REGISTRO ---
    if (isset($_POST['btn_nuevo'])) {
        $_SESSION['mostrar_form_carrera'] = true;
        redirectToForm();
    }

    // --- ACCIÓN: LISTAR TODAS LAS CARRERAS ---
    if (isset($_POST['btn_listar'])) {
        $query = "SELECT * FROM carrera ORDER BY PROGRAMA_ESTUDIO";
        $result = mysqli_query($cn, $query);
        $carreras = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $carreras[] = $row;
        }
        if (count($carreras) > 0) {
            $_SESSION['listado_carreras'] = $carreras;
        } else {
            $_SESSION['carrera_error'] = "No hay carreras registradas.";
        }
        redirectToForm();
    }
    
    // --- ACCIÓN: GUARDAR NUEVA CARRERA ---
    if (isset($_POST['btn_guardar'])) {
        $rd = $_POST['txt_rd'];
        $programa = $_POST['txt_programa_estudio'];
        $actividad = $_POST['txt_actividad_economica'];
        $titulo = $_POST['txt_titulo'];
        $ciclo = $_POST['txt_ciclo'];
        $creditos = $_POST['num_creditos'];
        $duracion = $_POST['num_duracion'];
        $estado = $_POST['sel_estado'];

        // Validación de existencia
        $query = "SELECT COUNT(*) FROM carrera WHERE PROGRAMA_ESTUDIO = ?";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $programa);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $_SESSION['carrera_error'] = "Error: El Programa de Estudio ya existe.";
        } else {
            $query = "INSERT INTO carrera (RESOLUCION_DIRECTORIAL, PROGRAMA_ESTUDIO, ACTIVIDAD_ECONOMICA, TITULO, CICLO, TOTAL_CREDITOS, DURACION_CARRERA, ESTADO) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "sssssiii", $rd, $programa, $actividad, $titulo, $ciclo, $creditos, $duracion, $estado);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['carrera_success'] = "Carrera registrada exitosamente.";
            } else {
                $_SESSION['carrera_error'] = "Error al registrar la carrera: " . mysqli_error($cn);
            }
            mysqli_stmt_close($stmt);
        }
        redirectToForm();
    }
    
    // --- ACCIÓN: EDITAR SELECCIONADO (Desde la lista) ---
    if (isset($_POST['btn_editar_seleccionado'])) {
        $id_carrera = $_POST['rd_elegir'] ?? null;
        if ($id_carrera) {
            $query = "SELECT * FROM carrera WHERE ID_CARRERA = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_carrera);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $carrera = mysqli_fetch_assoc($result);
            if ($carrera) {
                $_SESSION['carrera_encontrada'] = true;
                $_SESSION['carrera_data'] = $carrera;
            } else {
                $_SESSION['carrera_error'] = "Carrera no encontrada.";
            }
        } else {
            $_SESSION['carrera_error'] = "Por favor, seleccione una carrera para editar.";
        }
        redirectToForm();
    }

    // --- ACCIÓN: ACTUALIZAR CARRERA ---
    if (isset($_POST['btn_editar'])) {
        $id = $_POST['id_carrera'];
        $rd = $_POST['txt_rd'];
        $programa = $_POST['txt_programa_estudio'];
        $actividad = $_POST['txt_actividad_economica'];
        $titulo = $_POST['txt_titulo'];
        $ciclo = $_POST['txt_ciclo'];
        $creditos = $_POST['num_creditos'];
        $duracion = $_POST['num_duracion'];
        $estado = $_POST['sel_estado'];

        $query = "UPDATE carrera SET RESOLUCION_DIRECTORIAL = ?, PROGRAMA_ESTUDIO = ?, ACTIVIDAD_ECONOMICA = ?, TITULO = ?, CICLO = ?, TOTAL_CREDITOS = ?, DURACION_CARRERA = ?, ESTADO = ? WHERE ID_CARRERA = ?";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "sssssiiii", $rd, $programa, $actividad, $titulo, $ciclo, $creditos, $duracion, $estado, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['carrera_success'] = "Carrera actualizada exitosamente.";
        } else {
            $_SESSION['carrera_error'] = "Error al actualizar la carrera: " . mysqli_error($cn);
        }
        mysqli_stmt_close($stmt);
        redirectToForm();
    }

    // --- ACCIÓN: ELIMINAR CARRERA ---
    if (isset($_POST['btn_borrar']) || isset($_POST['btn_eliminar_seleccionado'])) {
        $id_carrera = $_POST['id_carrera'] ?? $_POST['rd_elegir'] ?? null;
        
        if ($id_carrera) {
            $query = "DELETE FROM carrera WHERE ID_CARRERA = ?";
            $stmt = mysqli_prepare($cn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_carrera);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['carrera_success'] = "Carrera eliminada exitosamente.";
            } else {
                $_SESSION['carrera_error'] = "Error al eliminar la carrera. Asegúrese de que no tenga módulos asociados.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['carrera_error'] = "Por favor, seleccione una carrera para eliminar.";
        }
        redirectToForm();
    }
}

mysqli_close($cn);
?>