<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmModulo_ocupacional.php

require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");

// Manejo de mensajes de sesión y limpieza
$mensaje_error = $_SESSION['modulo_error'] ?? '';
$mensaje_exito = $_SESSION['modulo_success'] ?? '';
unset($_SESSION['modulo_error']);
unset($_SESSION['modulo_success']);

// Variables para el formulario de edición
$modulo_a_editar = null;

// Lógica para editar un módulo (precarga los datos en el formulario)
if (isset($_POST['btn_editar']) && isset($_POST['id_modulo_editar'])) {
    try {
        $id_modulo_editar = $_POST['id_modulo_editar'];
        $query = "SELECT * FROM modulo_ocupacional WHERE ID_MODULO_OCUPACIONAL = ?";
        $stmt = mysqli_prepare($cn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_modulo_editar);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $modulo_a_editar = mysqli_fetch_assoc($result);
    } catch (Exception $e) {
        $mensaje_error = "Error al obtener los datos para editar: " . $e->getMessage();
    }
}

// Obtener la lista completa de módulos registrados
$modulos_totales = [];
try {
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    
    $query = "SELECT m.*, CONCAT(d.NOMBRES, ', ', d.APE_PATERNO,' ', d.APE_MATERNO) AS nombre_docente FROM modulo_ocupacional m JOIN docente d ON m.N_DOCUMENTO_DOCENTE = d.N_DOCUMENTO_DOCENTE ORDER BY m.MODULO";
    $result = mysqli_query($cn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $modulos_totales[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    $mensaje_error = "Error al cargar la lista de módulos: " . $e->getMessage();
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
    <title>Gestión de Módulos Ocupacionales</title>
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
            <h1>GESTIÓN DE MÓDULOS OCUPACIONALES</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <div class="listado-container">
                <h3>Lista de Módulos Registrados</h3>
                <table class="listado">
                    <thead>
                        <tr>
                            <th>ID Módulo</th>
                            <th>Nombre Módulo</th>
                            <th>Docente</th>
                            <th>Turno</th>
                            <th>Ciclo</th>
                            <th>Duración</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($modulos_totales)): ?>
                            <?php foreach ($modulos_totales as $modulo): ?>
                                <tr id="modulo-<?php echo htmlspecialchars($modulo['ID_MODULO_OCUPACIONAL']); ?>">
                                    <td><?php echo htmlspecialchars($modulo['ID_MODULO_OCUPACIONAL']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['MODULO']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['nombre_docente']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['TURNO']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['CICLO']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['DURACION']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['INICIO']); ?></td>
                                    <td><?php echo htmlspecialchars($modulo['FIN']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline-block;">
                                            <input type="hidden" name="id_modulo_editar" value="<?php echo htmlspecialchars($modulo['ID_MODULO_OCUPACIONAL']); ?>">
                                            <button type="submit" name="btn_editar" class="btn-editar">Editar</button>
                                        </form>
                                        <div style="display:inline-block;">
                                            <input type="hidden" class="id_modulo_eliminar" value="<?php echo htmlspecialchars($modulo['ID_MODULO_OCUPACIONAL']); ?>">
                                            <button type="button" class="btn-eliminar" onclick="eliminarModulo(this)">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No hay módulos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            
            <h4><?php echo ($modulo_a_editar) ? 'Editar Módulo' : 'Registrar Nuevo Módulo'; ?></h4>
            <form id="form_modulo" method="post" action="/avance/HTML/DATOS/modulo_ocupacional.php">
                <input type="hidden" name="id_modulo_oculto" value="<?php echo htmlspecialchars($modulo_a_editar['ID_MODULO_OCUPACIONAL'] ?? ''); ?>">
                
                <div class="form-section">
                    <div class="input-group">
                        <label>ID Módulo:</label>
                        <input type="text" name="txt_id_modulo" value="<?php echo htmlspecialchars($modulo_a_editar['ID_MODULO_OCUPACIONAL'] ?? ''); ?>" required <?php echo ($modulo_a_editar) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="input-group">
                        <label>Nombre del Módulo:</label>
                        <input type="text" name="txt_nombre" value="<?php echo htmlspecialchars($modulo_a_editar['MODULO'] ?? ''); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="select_docente_form">Docente:</label>
                        <select id="select_docente_form" name="id_docente" required>
                            <option value="">-- Seleccione un Docente --</option>
                            <?php foreach ($docentes as $docente): ?>
                                <option value="<?php echo htmlspecialchars($docente['N_DOCUMENTO_DOCENTE']); ?>" 
                                    <?php echo ($modulo_a_editar && $modulo_a_editar['N_DOCUMENTO_DOCENTE'] == $docente['N_DOCUMENTO_DOCENTE']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($docente['NOMBRES'] . ', ' . $docente['APE_PATERNO'] . ' ' . $docente['APE_MATERNO']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Turno:</label>
                        <input type="text" name="txt_turno" value="<?php echo htmlspecialchars($modulo_a_editar['TURNO'] ?? ''); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Ciclo:</label>
                        <input type="text" name="txt_ciclo" value="<?php echo htmlspecialchars($modulo_a_editar['CICLO'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Duración:</label>
                        <input type="number" name="txt_duracion" value="<?php echo htmlspecialchars($modulo_a_editar['DURACION'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Opción Ocupacional:</label>
                        <input type="text" name="txt_opcion_ocupacional" value="<?php echo htmlspecialchars($modulo_a_editar['OPCION_OCUPACIONAL'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Familia Profesional:</label>
                        <input type="text" name="txt_familia_profesional" value="<?php echo htmlspecialchars($modulo_a_editar['FAMILIA_PROFESIONAL'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Resolución Directoral:</label>
                        <input type="text" name="txt_resolucion_directorial" value="<?php echo htmlspecialchars($modulo_a_editar['RESOLUCION_DIRECTORIAL'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Fecha de Inicio:</label>
                        <input type="date" name="txt_inicio" value="<?php echo htmlspecialchars($modulo_a_editar['INICIO'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Fecha de Fin:</label>
                        <input type="date" name="txt_fin" value="<?php echo htmlspecialchars($modulo_a_editar['FIN'] ?? ''); ?>">
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="btn_guardar_modulo"><?php echo ($modulo_a_editar) ? 'ACTUALIZAR' : 'GUARDAR'; ?></button>
                    <a href="frmModulo_ocupacional.php" style="text-decoration: none;"><button type="button">CANCELAR</button></a>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        function eliminarModulo(button) {
            if (!confirm('¿Está seguro de que desea eliminar este módulo?')) {
                return;
            }

            const row = button.closest('tr');
            const idModulo = row.querySelector('.id_modulo_eliminar').value;

            const formData = new FormData();
            formData.append('id_modulo_eliminar', idModulo);
            formData.append('btn_eliminar', 'Eliminar');

            fetch('/avance/HTML/DATOS/modulo_ocupacional.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    row.remove();
                    alert('Módulo eliminado exitosamente.');
                } else {
                    return response.text().then(text => { throw new Error(text); });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al intentar eliminar el módulo. Revise los logs.');
            });
        }
    </script>
</body>
</html>