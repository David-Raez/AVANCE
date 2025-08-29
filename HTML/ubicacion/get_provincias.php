<?php
// get_provincias.php

$cn=mysqli_connect("localhost","root","");
	mysqli_select_db($cn,"cetpro");

header('Content-Type: application/json');

$departamento_nombre = $_GET['departamento'] ?? ''; // Recibimos el nombre del departamento

$provincias = [];

if (!empty($departamento_nombre)) {
    // Primero, obtener el ID_DEPARTAMENTO desde el nombre
    $stmt_dep_id = mysqli_prepare($cn, "SELECT ID_DEPARTAMENTO FROM departamento WHERE NOMBRE_DEPARTAMENTO = ?");
    mysqli_stmt_bind_param($stmt_dep_id, "s", $departamento_nombre);
    mysqli_stmt_execute($stmt_dep_id);
    $result_dep_id = mysqli_stmt_get_result($stmt_dep_id);
    $row_dep_id = mysqli_fetch_assoc($result_dep_id);
    $id_departamento = $row_dep_id['ID_DEPARTAMENTO'] ?? null;
    mysqli_stmt_close($stmt_dep_id);

    if ($id_departamento) {
        // Luego, obtener las provincias usando el ID_DEPARTAMENTO
        $stmt = mysqli_prepare($cn, "SELECT DISTINCT NOMBRE_PROVINCIA FROM provincia WHERE ID_DEPARTAMENTO = ? ORDER BY NOMBRE_PROVINCIA");
        mysqli_stmt_bind_param($stmt, "s", $id_departamento); // "s" si ID_DEPARTAMENTO es VARCHAR
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $provincias[] = $row['NOMBRE_PROVINCIA'];
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($cn);
echo json_encode($provincias);
?>