<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmUnidad_didactica.php
session_start();

// Incluir la conexión a la base de datos
require_once '../conexion.php';
include("../logeo/encabezado.php");

// Recupera los mensajes de sesión y los borra
$mensaje_error = $_SESSION['unidad_error'] ?? '';
$mensaje_exito = $_SESSION['unidad_success'] ?? '';
unset($_SESSION['unidad_error']);
unset($_SESSION['unidad_success']);

// Lógica para obtener las carreras (para el primer menú desplegable)
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Unidades Didácticas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/avance/HTML/PRESENTACION/estilo.css">
    <style>
        /* Estilos CSS (puedes reutilizar los de los formularios anteriores) */
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
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
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
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <?php include("/xampp/htdocs/avance/HTML/logeo/encabezado.php"); ?>
            <h1>GESTIÓN DE UNIDADES DIDÁCTICAS</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <div class="form-section">
                <div class="input-group">
                    <label for="select_carrera">Seleccionar Carrera</label>
                    <select id="select_carrera" name="id_carrera">
                        <option value="">-- Seleccione una Carrera --</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo htmlspecialchars($carrera['ID_CARRERA']); ?>">
                                <?php echo htmlspecialchars($carrera['PROGRAMA_ESTUDIO']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="select_modulo">Seleccionar Módulo</label>
                    <select id="select_modulo" name="id_modulo" disabled>
                        <option value="">-- Seleccione un Módulo --</option>
                    </select>
                </div>
            </div>

            <hr>

            <div id="unidades_container" style="display:none;">
                <h3>Unidades Didácticas del Módulo: <span id="nombre_modulo_seleccionado"></span></h3>
                
                <div class="action-buttons">
                    <button type="button" id="btn_nueva_unidad">NUEVA UNIDAD</button>
                </div>

                <div id="form_unidad_wrapper" style="display: none;">
                    <h4>Datos de la Unidad Didáctica</h4>
                    <form id="form_unidad" method="post" action="/avance/HTML/DATOS/unidad_didactica.php">
                        <input type="hidden" name="id_modulo_oculto" id="id_modulo_oculto">
                        <input type="hidden" name="id_unidad_oculto" id="id_unidad_oculto">
                        
                        <div class="form-section">
                            <div class="input-group">
                                <label>ID Unidad:</label>
                                <input type="text" name="txt_id_unidad" id="txt_id_unidad" required>
                            </div>
                            <div class="input-group">
                                <label>Nombre:</label>
                                <input type="text" name="txt_nombre" id="txt_nombre" required>
                            </div>
                            <div class="input-group">
                                <label>Clases:</label>
                                <input type="number" name="num_clases" id="num_clases">
                            </div>
                            <div class="input-group">
                                <label>Créditos:</label>
                                <input type="number" name="num_credito" id="num_credito">
                            </div>
                            <div class="input-group">
                                <label>Horas:</label>
                                <input type="number" name="num_horas" id="num_horas">
                            </div>
                            <div class="input-group">
                                <label>Fecha de Inicio:</label>
                                <input type="date" name="txt_inicio" id="txt_inicio">
                            </div>
                            <div class="input-group">
                                <label>Fecha de Fin:</label>
                                <input type="date" name="txt_fin" id="txt_fin">
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" name="btn_guardar_unidad" id="btn_guardar_unidad">GUARDAR</button>
                            <button type="button" id="btn_cancelar_unidad">CANCELAR</button>
                        </div>
                    </form>
                </div>

                <div class="listado-container">
                    <table class="listado" id="unidades_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Clases</th>
                                <th>Créditos</th>
                                <th>Horas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        // Lógica JavaScript
        const selectCarrera = document.getElementById('select_carrera');
        const selectModulo = document.getElementById('select_modulo');
        const unidadesContainer = document.getElementById('unidades_container');
        const unidadesTableBody = document.querySelector('#unidades_table tbody');
        const formUnidadWrapper = document.getElementById('form_unidad_wrapper');
        const btnNuevaUnidad = document.getElementById('btn_nueva_unidad');
        const btnCancelarUnidad = document.getElementById('btn_cancelar_unidad');
        const idModuloOculto = document.getElementById('id_modulo_oculto');
        const idUnidadOculto = document.getElementById('id_unidad_oculto');
        const nombreModuloSpan = document.getElementById('nombre_modulo_seleccionado');

        selectCarrera.addEventListener('change', async (e) => {
            const carreraId = e.target.value;
            selectModulo.innerHTML = '<option value="">-- Seleccione un Módulo --</option>';
            selectModulo.disabled = true;
            unidadesContainer.style.display = 'none';
            formUnidadWrapper.style.display = 'none';

            if (carreraId) {
                try {
                    const response = await fetch(`../DATOS/modulo_carrera.php?accion=listar&id_carrera=${carreraId}`);
                    const modulos = await response.json();
                    
                    if (modulos.length > 0) {
                        modulos.forEach(modulo => {
                            const option = document.createElement('option');
                            option.value = modulo.ID_MODULO;
                            option.textContent = modulo.NOMBRE;
                            selectModulo.appendChild(option);
                        });
                        selectModulo.disabled = false;
                    }
                } catch (error) {
                    console.error('Error al cargar módulos:', error);
                }
            }
        });

        selectModulo.addEventListener('change', async (e) => {
            const moduloId = e.target.value;
            const moduloText = e.target.options[e.target.selectedIndex].textContent;
            
            unidadesTableBody.innerHTML = '';
            formUnidadWrapper.style.display = 'none';

            if (moduloId) {
                nombreModuloSpan.textContent = moduloText;
                idModuloOculto.value = moduloId;
                unidadesContainer.style.display = 'block';
                await loadUnidades(moduloId);
            } else {
                unidadesContainer.style.display = 'none';
            }
        });

        btnNuevaUnidad.addEventListener('click', () => {
            document.getElementById('form_unidad').reset();
            idUnidadOculto.value = '';
            formUnidadWrapper.style.display = 'block';
            document.getElementById('btn_guardar_unidad').textContent = 'GUARDAR';
        });

        btnCancelarUnidad.addEventListener('click', () => {
            formUnidadWrapper.style.display = 'none';
        });

        async function loadUnidades(moduloId) {
            try {
                const response = await fetch(`../DATOS/unidad_didactica.php?accion=listar&id_modulo=${moduloId}`);
                const unidades = await response.json();
                
                unidadesTableBody.innerHTML = '';
                
                if (unidades.length > 0) {
                    unidades.forEach(unidad => {
                        const row = unidadesTableBody.insertRow();
                        row.innerHTML = `
                            <td>${unidad.ID_UNIDAD}</td>
                            <td>${unidad.NOMBRE_UNIDAD}</td>
                            <td>${unidad.CLASES}</td>
                            <td>${unidad.CREDITO}</td>
                            <td>${unidad.HORAS}</td>
                            <td>
                                <button onclick="editarUnidad('${unidad.ID_UNIDAD}', '${unidad.NOMBRE_UNIDAD}', '${unidad.CLASES}', '${unidad.CREDITO}', '${unidad.HORAS}', '${unidad.INICIO}', '${unidad.FIN}')">Editar</button>
                                <button onclick="eliminarUnidad('${unidad.ID_UNIDAD}')">Eliminar</button>
                            </td>
                        `;
                    });
                } else {
                    unidadesTableBody.innerHTML = '<tr><td colspan="6">No hay unidades didácticas registradas para este módulo.</td></tr>';
                }
            } catch (error) {
                console.error('Error al cargar unidades:', error);
                unidadesTableBody.innerHTML = '<tr><td colspan="6">Error al cargar los datos.</td></tr>';
            }
        }

        function editarUnidad(id, nombre, clases, credito, horas, inicio, fin) {
            document.getElementById('txt_id_unidad').value = id;
            document.getElementById('txt_nombre').value = nombre;
            document.getElementById('num_clases').value = clases;
            document.getElementById('num_credito').value = credito;
            document.getElementById('num_horas').value = horas;
            document.getElementById('txt_inicio').value = inicio;
            document.getElementById('txt_fin').value = fin;
            idUnidadOculto.value = id;
            formUnidadWrapper.style.display = 'block';
            document.getElementById('btn_guardar_unidad').textContent = 'ACTUALIZAR';
        }

        async function eliminarUnidad(id) {
            if (confirm('¿Está seguro de que desea eliminar esta unidad didáctica?')) {
                const formData = new FormData();
                formData.append('btn_eliminar_unidad', 'true');
                formData.append('id_unidad', id);
                
                try {
                    const response = await fetch('../DATOS/unidad_didactica.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.text();
                    console.log(result);
                    const moduloId = document.getElementById('select_modulo').value;
                    if (moduloId) {
                        await loadUnidades(moduloId);
                    }
                } catch (error) {
                    console.error('Error al eliminar la unidad:', error);
                    alert('Error al eliminar la unidad.');
                }
            }
        }
    </script>
</body>
</html>