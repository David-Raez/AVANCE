<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmGestion_matricula.php

require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");

// Limpia los resultados de consultas previas para evitar errores.
while (mysqli_more_results($cn) && mysqli_next_result($cn));

$mensaje_exito = $_SESSION['gestion_success'] ?? '';
$mensaje_error = $_SESSION['gestion_error'] ?? '';
unset($_SESSION['gestion_success']);
unset($_SESSION['gestion_error']);

$matriculas = [];

// Consulta para obtener las matrículas y los datos del alumno.
$query = "SELECT * FROM matricula m ORDER BY m.FECHA_REGISTRO DESC";

try {
    $result = mysqli_query($cn, $query);
    if ($result) {
        while ($row = mysqli_fetch_row($result)) {
            $matriculas[] = $row;
        }
        mysqli_free_result($result);
    } else {
        $mensaje_error = "Error al ejecutar la consulta principal: " . mysqli_error($cn);
        error_log($mensaje_error);
    }
} catch (Exception $e) {
    error_log("Excepción al cargar matrículas: " . $e->getMessage());
    $mensaje_error = "Excepción al cargar los datos de matrícula.";
}

// Ahora, obtenemos los nombres de los programas de forma separada.
$carreras = [];
$modulos = [];
$formaciones = [];

// Se limpian los resultados entre consultas para evitar errores.
while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_carreras = mysqli_query($cn, "SELECT ID_CARRERA, PROGRAMA_ESTUDIO FROM carrera");
if ($result_carreras) {
    while ($row = mysqli_fetch_assoc($result_carreras)) {
        $carreras[$row['ID_CARRERA']] = $row['PROGRAMA_ESTUDIO'];
    }
    mysqli_free_result($result_carreras);
}

while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_modulos = mysqli_query($cn, "SELECT ID_MODULO_OCUPACIONAL, MODULO FROM modulo_ocupacional");
if ($result_modulos) {
    while ($row = mysqli_fetch_assoc($result_modulos)) {
        $modulos[$row['ID_MODULO_OCUPACIONAL']] = $row['MODULO'];
    }
    mysqli_free_result($result_modulos);
}

while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_formaciones = mysqli_query($cn, "SELECT ID_FORM, NOMBRE_FORMACION FROM formacion_continua");
if ($result_formaciones) {
    while ($row = mysqli_fetch_assoc($result_formaciones)) {
        $formaciones[$row['ID_FORM']] = $row['NOMBRE_FORMACION'];
    }
    mysqli_free_result($result_formaciones);
}

mysqli_close($cn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Matrículas</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .table-container { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
        .action-buttons a { margin-right: 5px; }
        .btn-action { padding: 5px 10px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; }
        .btn-delete { background-color: #e74c3c; }
        .btn-delete:hover { background-color: #c0392b; }
        .btn-edit { background-color: #3498db; }
        .btn-edit:hover { background-color: #2980b9; }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>GESTIÓN DE MATRÍCULAS</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alumno</th>
                            <th>Tipo</th>
                            <th>Programa</th>
                            <th>Becado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriculas as $mat):
                            $tipo_programa = '';
                            $nombre_programa = '';
                            if (!empty($mat['ID_MODULO'])) {
                                $tipo_programa = 'Carrera';
                                $nombre_programa = $carreras[$mat['ID_MODULO']] ?? 'No encontrado';
                            } elseif (!empty($mat['ID_MODULO_OCUPACIONAL'])) {
                                $tipo_programa = 'Módulo Ocupacional';
                                $nombre_programa = $modulos[$mat['ID_MODULO_OCUPACIONAL']] ?? 'No encontrado';
                            } elseif (!empty($mat['ID_FORM'])) {
                                $tipo_programa = 'Formación Continua';
                                $nombre_programa = $formaciones[$mat['ID_FORM']] ?? 'No encontrado';
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mat['ID_MATRICULA']); ?></td>
                            <td><?php echo htmlspecialchars($mat['NOMBRE_ALUMNO']); ?></td>
                            <td><?php echo htmlspecialchars($tipo_programa); ?></td>
                            <td><?php echo htmlspecialchars($nombre_programa); ?></td>
                            <td><?php echo ($mat['BECADO'] == 1) ? 'Sí' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($mat['FECHA_MATRICULA']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-edit">Editar</a>
                                    <a href="#" class="btn-action btn-delete" onclick="confirmarEliminar(<?php echo htmlspecialchars($mat['ID_MATRICULA']); ?>)">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($matriculas)): ?>
                            <tr>
                                <td colspan="7">No se encontraron matrículas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        function confirmarEliminar(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta matrícula? Esta acción no se puede deshacer.")) {
                window.location.href = `../DATOS/gestion_matricula.php?accion=eliminar&id=${id}`;
            }
        }
    </script>
</body>
</html>