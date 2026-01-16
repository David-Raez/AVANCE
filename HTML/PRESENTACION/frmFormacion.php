<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmFormacion_continua.php

require_once '../conexion.php';
include("../logeo/encabezado.php");

// Manejo de mensajes de sesión y limpieza
$mensaje_error = $_SESSION['fc_error'] ?? '';
$mensaje_exito = $_SESSION['fc_success'] ?? '';
unset($_SESSION['fc_error']);
unset($_SESSION['fc_success']);

// Variables para el formulario de edición
$formacion_a_editar = null;

// Lógica para editar una formación (precarga los datos en el formulario)
if (isset($_POST['btn_editar']) && isset($_POST['id_formacion_editar'])) {
    try {
        $id_formacion_editar = $_POST['id_formacion_editar'];
        $query = "SELECT * FROM formacion_continua WHERE ID_FORM = ?";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_formacion_editar);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $formacion_a_editar = mysqli_fetch_assoc($result);
    } catch (Exception $e) {
        $mensaje_error = "Error al obtener los datos para editar: " . $e->getMessage();
    }
}

// Obtener la lista completa de formaciones continuas
$formaciones_totales = [];
try {
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    
    $query = "SELECT fc.*, CONCAT(d.NOMBRES, ', ', d.APE_PATERNO,' ', d.APE_MATERNO) AS nombre_docente FROM formacion_continua fc JOIN docente d ON fc.N_DOCUMENTO_DOCENTE = d.N_DOCUMENTO_DOCENTE ORDER BY fc.FECHA_REGISTRO DESC";
    $result = mysqli_query($cn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formaciones_totales[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    $mensaje_error = "Error al cargar la lista de formaciones: " . $e->getMessage();
}

// Obtener la lista de docentes para el combobox
$docentes = [];
try {
    $result_docentes = mysqli_query($cn, "SELECT N_DOCUMENTO_DOCENTE, NOMBRES, APE_PATERNO, APE_MATERNO FROM docente ORDER BY NOMBRES");
    if ($result_docentes) {
        while ($row = mysqli_fetch_assoc($result_docentes)) {
            $docentes[] = $row;
        }
        mysqli_free_result($result_docentes);
    }
} catch (Exception $e) {
    error_log("Excepción al obtener docentes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Formación Continua</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos CSS proporcionados anteriormente */
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50e3c2;
            --text-color: #333;
            --light-bg: #f4f7f6;
            --dark-bg: #e9ecef;
            --border-color: #ddd;
            --border-radius: 8px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid var(--border-color);
            box-shadow: var(--box-shadow);
        }
        h1 {
            color: var(--primary-color);
            font-weight: 600;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
        }
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .form-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .input-group {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        input[type="text"], input[type="number"], select, input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--light-bg);
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, select:focus, input[type="date"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }
        .action-buttons button, .action-buttons input[type="submit"] {
            width: auto;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .listado-container {
            overflow-x: auto;
            margin-top: 2rem;
        }
        table.listado {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            text-align: left;
        }
        table.listado th, table.listado td {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
        }
        table.listado thead tr {
            background-color: var(--primary-color);
            color: #fff;
            text-align: left;
            font-weight: 600;
        }
        table.listado tbody tr:nth-of-type(even) {
            background-color: var(--dark-bg);
        }
        table.listado tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>GESTIÓN DE FORMACIÓN CONTINUA</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <div class="listado-container">
                <h3>Lista de Formaciones Continuas Registradas</h3>
                <table class="listado">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Docente</th>
                            <th>Familia</th>
                            <th>Nombre</th>
                            <th>Módulo</th>
                            <th>Modalidad</th>
                            <th>Créditos</th>
                            <th>Horas</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($formaciones_totales)): ?>
                            <?php foreach ($formaciones_totales as $formacion): ?>
                                <tr id="formacion-<?php echo htmlspecialchars($formacion['ID_FORM']); ?>">
                                    <td><?php echo htmlspecialchars($formacion['ID_FORM']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['nombre_docente']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['FAMILIA_PRODUCTIVA']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['NOMBRE_FORMACION']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['MODULO_UNIDAD']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['MODALIDAD']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['CREDITOS']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['HORAS']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['INICIO']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['FIN']); ?></td>
                                    <td><?php echo htmlspecialchars($formacion['ESTADO'] == 1 ? 'Activo' : 'Inactivo'); ?></td>
                                    <td>
                                        <form method="post" style="display:inline-block;">
                                            <input type="hidden" name="id_formacion_editar" value="<?php echo htmlspecialchars($formacion['ID_FORM']); ?>">
                                            <button type="submit" name="btn_editar" class="btn-editar">Editar</button>
                                        </form>
                                        <div style="display:inline-block;">
                                            <input type="hidden" class="id_formacion_eliminar" value="<?php echo htmlspecialchars($formacion['ID_FORM']); ?>">
                                            <button type="button" class="btn-eliminar" onclick="eliminarFormacion(this)">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12">No hay formaciones continuas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            
            <h4><?php echo ($formacion_a_editar) ? 'Editar Formación Continua' : 'Registrar Nueva Formación Continua'; ?></h4>
            <form id="form_formacion" method="post" action="/avance/HTML/DATOS/formacion_continua.php">
                <input type="hidden" name="id_form_oculto" value="<?php echo htmlspecialchars($formacion_a_editar['ID_FORM'] ?? ''); ?>">
                
                <div class="form-section">
                    <div class="input-group">
                        <label>ID Formación:</label>
                        <input type="text" name="txt_id_form" value="<?php echo htmlspecialchars($formacion_a_editar['ID_FORM'] ?? ''); ?>" required <?php echo ($formacion_a_editar) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="input-group">
                        <label for="select_docente_form">Docente:</label>
                        <select id="select_docente_form" name="id_docente" required>
                            <option value="">-- Seleccione un Docente --</option>
                            <?php foreach ($docentes as $docente): ?>
                                <option value="<?php echo htmlspecialchars($docente['N_DOCUMENTO_DOCENTE']); ?>" 
                                    <?php echo ($formacion_a_editar && $formacion_a_editar['N_DOCUMENTO_DOCENTE'] == $docente['N_DOCUMENTO_DOCENTE']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($docente['NOMBRES'] . ', ' . $docente['APE_PATERNO'] . ' ' . $docente['APE_MATERNO']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Familia Productiva:</label>
                        <input type="text" name="txt_familia_productiva" value="<?php echo htmlspecialchars($formacion_a_editar['FAMILIA_PRODUCTIVA'] ?? ''); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Nombre de la Formación:</label>
                        <input type="text" name="txt_nombre_formacion" value="<?php echo htmlspecialchars($formacion_a_editar['NOMBRE_FORMACION'] ?? ''); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Módulo/Unidad:</label>
                        <input type="text" name="txt_modulo_unidad" value="<?php echo htmlspecialchars($formacion_a_editar['MODULO_UNIDAD'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Modalidad:</label>
                        <input type="text" name="txt_modalidad" value="<?php echo htmlspecialchars($formacion_a_editar['MODALIDAD'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Créditos:</label>
                        <input type="number" name="txt_creditos" value="<?php echo htmlspecialchars($formacion_a_editar['CREDITOS'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Horas:</label>
                        <input type="number" name="txt_horas" value="<?php echo htmlspecialchars($formacion_a_editar['HORAS'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Fecha de Inicio:</label>
                        <input type="date" name="txt_inicio" value="<?php echo htmlspecialchars($formacion_a_editar['INICIO'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Fecha de Fin:</label>
                        <input type="date" name="txt_fin" value="<?php echo htmlspecialchars($formacion_a_editar['FIN'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Estado:</label>
                        <select name="txt_estado" required>
                            <option value="1" <?php echo ($formacion_a_editar && $formacion_a_editar['ESTADO'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($formacion_a_editar && $formacion_a_editar['ESTADO'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="btn_guardar_formacion"><?php echo ($formacion_a_editar) ? 'ACTUALIZAR' : 'GUARDAR'; ?></button>
                    <a href="frmFormacion.php" style="text-decoration: none;"><button type="button">CANCELAR</button></a>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        function eliminarFormacion(button) {
            if (!confirm('¿Está seguro de que desea eliminar esta formación?')) {
                return;
            }

            const row = button.closest('tr');
            const idFormacion = row.querySelector('.id_formacion_eliminar').value;

            const formData = new FormData();
            formData.append('id_formacion_eliminar', idFormacion);
            formData.append('btn_eliminar', 'Eliminar');

            fetch('../DATOS/formacion_continua.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    row.remove();
                    alert('Formación eliminada exitosamente.');
                } else {
                    return response.text().then(text => { throw new Error(text); });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al intentar eliminar la formación. Revise los logs.');
            });
        }
    </script>
</body>
</html>