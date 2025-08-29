<?php
// get_distritos.php

$cn=mysqli_connect("localhost","root","");
	mysqli_select_db($cn,"cetpro"); // Asegúrate de que tu archivo de conexión esté bien configurado

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 1. Recibir los nombres de departamento y provincia desde la solicitud AJAX
$departamento_nombre_recibido = $_GET['departamento'] ?? '';
$provincia_nombre_recibida = $_GET['provincia'] ?? '';

$distritos = [];

if (!empty($departamento_nombre_recibido) && !empty($provincia_nombre_recibida)) {
    // 2. Obtener el ID_DEPARTAMENTO a partir del nombre del departamento
    $id_departamento = null;
    $stmt_dep = mysqli_prepare($cn, "SELECT ID_DEPARTAMENTO FROM departamento WHERE NOMBRE_DEPARTAMENTO = ?");
    if ($stmt_dep) {
        mysqli_stmt_bind_param($stmt_dep, "s", $departamento_nombre_recibido);
        mysqli_stmt_execute($stmt_dep);
        $result_dep = mysqli_stmt_get_result($stmt_dep);
        $row_dep = mysqli_fetch_assoc($result_dep);
        $id_departamento = $row_dep['ID_DEPARTAMENTO'] ?? null;
        mysqli_stmt_close($stmt_dep);
    } else {
        error_log("Error al preparar consulta ID_DEPARTAMENTO en get_distritos: " . mysqli_error($cn));
    }

    if ($id_departamento) {
        // 3. Obtener el ID_PROVINCIA a partir del nombre de la provincia y el ID_DEPARTAMENTO
        $id_provincia = null;
        // Importante: unimos con departamento para asegurar que la provincia pertenece al departamento correcto
        $stmt_prov = mysqli_prepare($cn, "SELECT P.ID_PROVINCIA FROM provincia P JOIN departamento D ON P.ID_DEPARTAMENTO = D.ID_DEPARTAMENTO WHERE P.NOMBRE_PROVINCIA = ? AND D.ID_DEPARTAMENTO = ?");
        if ($stmt_prov) {
            // Asume que ID_DEPARTAMENTO es VARCHAR (s) o INT (i)
            mysqli_stmt_bind_param($stmt_prov, "ss", $provincia_nombre_recibida, $id_departamento); // Cambia "s" a "i" si ID_DEPARTAMENTO es INT
            mysqli_stmt_execute($stmt_prov);
            $result_prov = mysqli_stmt_get_result($stmt_prov);
            $row_prov = mysqli_fetch_assoc($result_prov);
            $id_provincia = $row_prov['ID_PROVINCIA'] ?? null;
            mysqli_stmt_close($stmt_prov);
        } else {
            error_log("Error al preparar consulta ID_PROVINCIA en get_distritos: " . mysqli_error($cn));
        }

        if ($id_provincia) {
            // 4. Obtener los distritos usando ID_DEPARTAMENTO y ID_PROVINCIA
            // SELECT ID_DISTRITO as ubigeo, NOMBRE_DISTRITO as nombre
            // Desde la tabla 'distrito'
            // Donde ID_DEPARTAMENTO coincide
            // Y ID_PROVINCIA coincide
            // Ordenar por NOMBRE_DISTRITO
            $stmt_dist = mysqli_prepare($cn, "SELECT ID_DISTRITO as ubigeo, NOMBRE_DISTRITO as nombre FROM distrito WHERE ID_DEPARTAMENTO = ? AND ID_PROVINCIA = ? ORDER BY NOMBRE_DISTRITO");
            if ($stmt_dist) {
                // Asume que ID_DEPARTAMENTO e ID_PROVINCIA son VARCHAR (s) o INT (i)
                mysqli_stmt_bind_param($stmt_dist, "ss", $id_departamento, $id_provincia); // Cambia "s" a "i" si tus IDs son INT
                mysqli_stmt_execute($stmt_dist);
                $result_dist = mysqli_stmt_get_result($stmt_dist);

                while ($row = mysqli_fetch_assoc($result_dist)) {
                    $distritos[] = ['ubigeo' => $row['ubigeo'], 'nombre' => $row['nombre']];
                }
                mysqli_stmt_close($stmt_dist);
            } else {
                error_log("Error al preparar consulta de distritos: " . mysqli_error($cn));
            }
        }
    }
}

mysqli_close($cn); // Cerrar la conexión después de usarla

echo json_encode($distritos); // Devolver el array de distritos como JSON
?>