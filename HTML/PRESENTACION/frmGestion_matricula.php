<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmGestion_matricula.php

require_once '../conexion.php';
include("../logeo/encabezado.php");

// Limpia los resultados de consultas previas
while (mysqli_more_results($cn) && mysqli_next_result($cn));

$mensaje_exito = $_SESSION['gestion_success'] ?? '';
$mensaje_error = $_SESSION['gestion_error'] ?? '';
unset($_SESSION['gestion_success']);
unset($_SESSION['gestion_error']);

$matriculas = [];

// --- CORRECCIÓN CRÍTICA ---
// 1. Usamos N_DOCUMENTO_ALUMNO para unir con la tabla alumno (verifica que en 'alumno' la columna sea 'N_DOCUMENTO' o 'DNI').
// 2. Traemos apellidos para mostrar el nombre completo.
$query = "SELECT m.*, a.NOMBRES, a.APE_PATERNO, a.APE_MATERNO 
          FROM matricula m 
          LEFT JOIN alumno a ON m.N_DOCUMENTO_ALUMNO = a.N_DOCUMENTO_ALUMNO 
          ORDER BY m.FECHA_REGISTRO DESC";

try {
    $result = mysqli_query($cn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $matriculas[] = $row;
        }
        mysqli_free_result($result);
    } else {
        // Si falla, mostramos el error SQL para depurar
        $mensaje_error = "Error SQL: " . mysqli_error($cn);
    }
} catch (Exception $e) {
    $mensaje_error = "Excepción: " . $e->getMessage();
}

// Obtener catálogos para mostrar nombres de programas
$carreras = [];
$modulos = [];
$formaciones = [];

// Limpieza de buffer
while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_carreras = mysqli_query($cn, "SELECT ID_CARRERA, PROGRAMA_ESTUDIO FROM carrera");
if ($result_carreras) {
    while ($row = mysqli_fetch_assoc($result_carreras)) {
        $carreras[$row['ID_CARRERA']] = $row['PROGRAMA_ESTUDIO'];
    }
}

while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_modulos = mysqli_query($cn, "SELECT ID_MODULO_OCUPACIONAL, MODULO FROM modulo_ocupacional");
if ($result_modulos) {
    while ($row = mysqli_fetch_assoc($result_modulos)) {
        $modulos[$row['ID_MODULO_OCUPACIONAL']] = $row['MODULO'];
    }
}

while (mysqli_more_results($cn) && mysqli_next_result($cn));
$result_formaciones = mysqli_query($cn, "SELECT ID_FORM, NOMBRE_FORMACION FROM formacion_continua");
if ($result_formaciones) {
    while ($row = mysqli_fetch_assoc($result_formaciones)) {
        $formaciones[$row['ID_FORM']] = $row['NOMBRE_FORMACION'];
    }
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
                            <th>DNI Alumno</th>
                            <th>Nombre Alumno</th>
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
                            
                            // Determinamos el programa
                            if (!empty($mat['ID_MODULO'])) {
                                $tipo_programa = 'Carrera';
                                $nombre_programa = $carreras[$mat['ID_MODULO']] ?? $mat['ID_MODULO'];
                            } elseif (!empty($mat['ID_MODULO_OCUPACIONAL'])) {
                                $tipo_programa = 'Módulo Ocupacional';
                                $nombre_programa = $modulos[$mat['ID_MODULO_OCUPACIONAL']] ?? $mat['ID_MODULO_OCUPACIONAL'];
                            } elseif (!empty($mat['ID_FORM'])) {
                                $tipo_programa = 'Formación Continua';
                                $nombre_programa = $formaciones[$mat['ID_FORM']] ?? $mat['ID_FORM'];
                            } else {
                                $tipo_programa = 'Sin asignar';
                                $nombre_programa = '---';
                            }
                            
                            // Construimos nombre completo si existen los campos, si no, usa el DNI
                            $nombreCompleto = $mat['NOMBRE_ALUMNO'] ?? '';
                            if (isset($mat['APELLIDO_PATERNO'])) $nombreCompleto .= ' ' . $mat['APELLIDO_PATERNO'];
                            if (isset($mat['APELLIDO_MATERNO'])) $nombreCompleto .= ' ' . $mat['APELLIDO_MATERNO'];
                            
                            if (trim($nombreCompleto) == '') {
                                $nombreCompleto = "Alumno (" . $mat['N_DOCUMENTO_ALUMNO'] . ")";
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mat['ID_MATRICULA']); ?></td>
                            <td><?php echo htmlspecialchars($mat['N_DOCUMENTO_ALUMNO']); ?></td>
                            <td><?php echo htmlspecialchars($nombreCompleto); ?></td>
                            <td><?php echo htmlspecialchars($tipo_programa); ?></td>
                            <td><?php echo htmlspecialchars($nombre_programa); ?></td>
                            <td><?php echo ($mat['BECADO'] == 1) ? 'Sí' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($mat['FECHA_REGISTRO']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="frmEditar_matricula.php?id=<?php echo $mat['ID_MATRICULA']; ?>" class="btn-action btn-edit">Editar</a>
                                    <a href="#" class="btn-action btn-delete" onclick="confirmarEliminar(<?php echo htmlspecialchars($mat['ID_MATRICULA']); ?>)">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($matriculas)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;">
                                    No se encontraron registros. <br>
                                    <small>(Verifica que la tabla 'alumno' tenga la columna 'N_DOCUMENTO' coincidente)</small>
                                </td>
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
            if (confirm("¿Estás seguro de que deseas eliminar esta matrícula?")) {
                window.location.href = `../DATOS/gestion_matricula.php?accion=eliminar&id=${id}`;
            }
        }
    </script>
</body>
</html>