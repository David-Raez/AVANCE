<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmMatricula.php
require_once '../conexion.php';
include("../logeo/encabezado.php");

$mensaje_error = $_SESSION['matricula_error'] ?? '';
$mensaje_exito = $_SESSION['matricula_success'] ?? '';
$alumno_data = $_SESSION['alumno_data'] ?? [];

unset($_SESSION['matricula_error']);
unset($_SESSION['matricula_success']);
unset($_SESSION['alumno_data']);

$carreras = [];
try {
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    
    $result = mysqli_query($cn, "SELECT ID_CARRERA, PROGRAMA_ESTUDIO FROM carrera ORDER BY PROGRAMA_ESTUDIO");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $carreras[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    error_log("Excepción al obtener carreras: " . $e->getMessage());
}

$modulos_ocupacionales = [];
try {
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    $result = mysqli_query($cn, "SELECT ID_MODULO_OCUPACIONAL, MODULO FROM modulo_ocupacional ORDER BY MODULO");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $modulos_ocupacionales[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    error_log("Error al cargar módulos ocupacionales: " . $e->getMessage());
}

$formaciones_continuas = [];
try {
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    $result = mysqli_query($cn, "SELECT ID_FORM, NOMBRE_FORMACION FROM formacion_continua ORDER BY NOMBRE_FORMACION");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formaciones_continuas[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    error_log("Error al cargar formaciones continuas: " . $e->getMessage());
}

mysqli_close($cn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Matrícula</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/avance/HTML/PRESENTACION/estilo.css">
    <style>
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
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--light-bg); color: var(--text-color); display: flex; flex-direction: column; min-height: 100vh; }
        header { background-color: #fff; padding: 20px; text-align: center; border-bottom: 2px solid var(--border-color); box-shadow: var(--box-shadow); }
        h1 { color: var(--primary-color); font-weight: 600; }
        .container { width: 90%; max-width: 800px; margin: 2rem auto; padding: 2rem; background-color: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); }
        .message { padding: 15px; margin-bottom: 20px; border-radius: var(--border-radius); font-weight: 600; }
        .message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .form-section { display: flex; flex-direction: column; gap: 1.5rem; }
        .input-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 0.5rem; color: var(--text-color); }
        input[type="text"], select { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--border-radius); background-color: var(--light-bg); transition: all 0.3s ease; }
        .action-buttons { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem; }
        .action-buttons button, .action-buttons input[type="submit"] { padding: 10px 20px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; }
        .btn-verde { background-color: #50e3c2; color: white; }
        .btn-verde:hover { background-color: #41b49e; }
        .btn-azul { background-color: #4a90e2; color: white; }
        .btn-azul:hover { background-color: #3b74b8; }
        .card-info { border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 20px; margin-top: 20px; background-color: var(--dark-bg); }
        .radio-group { display: flex; gap: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>PROCESO DE MATRÍCULA</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <form method="post" action="/avance/HTML/DATOS/matricula.php">
                <div class="form-section">
                    <div class="input-group">
                        <label for="txt_documento">NÚMERO DE DOCUMENTO DEL ALUMNO:</label>
                        <input type="text" name="txt_documento" id="txt_documento" required>
                    </div>
                    <div class="action-buttons">
                        <button type="button" id="btn_buscar_alumno" class="btn-azul">BUSCAR ALUMNO</button>
                        <a href="frmGestion_matricula.php" class="btn-azul">GESTIONAR MATRICULAS</a>
                    </div>
                </div>

                <div id="alumno_info" style="display:none;">
                    <div class="card-info">
                        <h4>Información del Alumno</h4>
                        <p><strong>N° Documento:</strong> <span id="info_documento"></span></p>
                        <p><strong>Nombre Completo:</strong> <span id="info_nombre"></span></p>
                    </div>
                </div>

                <div id="matricula_form_wrapper" style="display:none;">
                    <hr>
                    <div class="form-section">
                        <input type="hidden" name="n_documento_alumno" id="input_n_documento">
                        
                        <div class="input-group">
                            <label>MATRICULAR EN:</label>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="matricula_tipo" value="carrera" checked> Carrera
                                </label>
                                <label>
                                    <input type="radio" name="matricula_tipo" value="modulo"> Módulo Ocupacional
                                </label>
                                <label>
                                    <input type="radio" name="matricula_tipo" value="formacion"> Formación Continua
                                </label>
                            </div>
                        </div>

                        <div class="input-group" id="carrera_select_group">
                            <label for="select_carrera">SELECCIONE UNA CARRERA:</label>
                            <select name="select_carrera" id="select_carrera">
                                <option value="">-- Seleccione una Carrera --</option>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?php echo htmlspecialchars($carrera['ID_CARRERA']); ?>">
                                        <?php echo htmlspecialchars($carrera['PROGRAMA_ESTUDIO']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group" id="modulo_select_group" style="display:none;">
                            <label for="select_modulo">SELECCIONE UN MÓDULO OCUPACIONAL:</label>
                            <select name="select_modulo" id="select_modulo">
                                <option value="">-- Seleccione un Módulo --</option>
                                <?php foreach ($modulos_ocupacionales as $modulo): ?>
                                    <option value="<?php echo htmlspecialchars($modulo['ID_MODULO_OCUPACIONAL']); ?>">
                                        <?php echo htmlspecialchars($modulo['MODULO']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="input-group" id="formacion_select_group" style="display:none;">
                            <label for="select_formacion">SELECCIONE UNA FORMACIÓN CONTINUA:</label>
                            <select name="select_formacion" id="select_formacion">
                                <option value="">-- Seleccione una Formación --</option>
                                <?php foreach ($formaciones_continuas as $formacion): ?>
                                    <option value="<?php echo htmlspecialchars($formacion['ID_FORM']); ?>">
                                        <?php echo htmlspecialchars($formacion['NOMBRE_FORMACION']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="checkbox_becado">¿ES BECADO?</label>
                            <input type="checkbox" name="checkbox_becado" id="checkbox_becado" value="1">
                        </div>
                    </div>
                    <div class="action-buttons">
                        <input type="submit" name="btn_matricular" value="MATRICULAR" class="btn-verde">
                    </div>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        const btnBuscarAlumno = document.getElementById('btn_buscar_alumno');
        const txtDocumento = document.getElementById('txt_documento');
        const alumnoInfo = document.getElementById('alumno_info');
        const matriculaFormWrapper = document.getElementById('matricula_form_wrapper');
        const inputDocumentoAlumno = document.getElementById('input_n_documento');

        // Referencias a los grupos de radio buttons y selects
        const radioButtons = document.querySelectorAll('input[name="matricula_tipo"]');
        const carreraSelectGroup = document.getElementById('carrera_select_group');
        const moduloSelectGroup = document.getElementById('modulo_select_group');
        const formacionSelectGroup = document.getElementById('formacion_select_group');

        // Lógica de visibilidad de los selects
        function mostrarOcultar() {
            const tipoSeleccionado = document.querySelector('input[name="matricula_tipo"]:checked').value;
            
            carreraSelectGroup.style.display = 'none';
            moduloSelectGroup.style.display = 'none';
            formacionSelectGroup.style.display = 'none';

            document.getElementById('select_carrera').removeAttribute('required');
            document.getElementById('select_modulo').removeAttribute('required');
            document.getElementById('select_formacion').removeAttribute('required');

            if (tipoSeleccionado === 'carrera') {
                carreraSelectGroup.style.display = 'block';
                document.getElementById('select_carrera').setAttribute('required', 'required');
            } else if (tipoSeleccionado === 'modulo') {
                moduloSelectGroup.style.display = 'block';
                document.getElementById('select_modulo').setAttribute('required', 'required');
            } else if (tipoSeleccionado === 'formacion') {
                formacionSelectGroup.style.display = 'block';
                document.getElementById('select_formacion').setAttribute('required', 'required');
            }
        }
        radioButtons.forEach(radio => radio.addEventListener('change', mostrarOcultar));
        
        // Lógica del buscador de alumno
        btnBuscarAlumno.addEventListener('click', async () => {
            const documento = txtDocumento.value;
            if (!documento) {
                alert('Por favor, ingrese el número de documento.');
                return;
            }

            try {
                const response = await fetch(`../DATOS/matricula.php?accion=buscar_alumno&documento=${documento}`);
                const alumno = await response.json();

                if (alumno.error) {
                    alert(alumno.error);
                    alumnoInfo.style.display = 'none';
                    matriculaFormWrapper.style.display = 'none';
                } else {
                    document.getElementById('info_nombre').textContent = alumno.NOMBRES;
                    document.getElementById('info_documento').textContent = alumno.N_DOCUMENTO_ALUMNO;
                    
                    inputDocumentoAlumno.value = alumno.N_DOCUMENTO_ALUMNO;

                    alumnoInfo.style.display = 'block';
                    matriculaFormWrapper.style.display = 'block';

                    mostrarOcultar();
                }
            } catch (error) {
                console.error('Error al buscar el alumno:', error);
                alert('Error al buscar el alumno. Intente de nuevo.');
            }
        });
    </script>
</body>
</html>