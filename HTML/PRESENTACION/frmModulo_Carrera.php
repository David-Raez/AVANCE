<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmModulo_carrera.php


// Incluir la conexión a la base de datos
require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");
// Recupera los mensajes de sesión y los borra
$mensaje_error = $_SESSION['modulo_error'] ?? '';
$mensaje_exito = $_SESSION['modulo_success'] ?? '';
unset($_SESSION['modulo_error']);
unset($_SESSION['modulo_success']);

// Lógica para obtener las carreras (para el menú desplegable)
$carreras = [];
try {
    // Es buena práctica vaciar resultados previos si existieran
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
    <title>Gestión de Módulos por Carrera</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/avance/HTML/PRESENTACION/estilo.css">
    <style>
        /* Estilos CSS (puedes reutilizar los de frmCarrera.php) */
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
        header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
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
        .action-buttons input[type="submit"], button {
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
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            
            <h1>GESTIÓN DE MÓDULOS POR CARRERA</h1>
        </header>

        <div class="container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <form id="form_seleccionar_carrera">
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
                </div>
            </form>

            <hr>

            <div id="modulos_container" style="display:none;">
                <h3>Módulos de la Carrera: <span id="nombre_carrera_seleccionada"></span></h3>
                
                <div class="action-buttons">
                    <button type="button" id="btn_nuevo_modulo">NUEVO MÓDULO</button>
                </div>

                <div id="form_modulo_wrapper" style="display: none;">
                    <h4>Datos del Módulo</h4>
                    <form id="form_modulo" method="post" action="/avance/HTML/DATOS/modulo_carrera.php">
                        <input type="hidden" name="id_carrera_oculto" id="id_carrera_oculto">
                        <input type="hidden" name="id_modulo_oculto" id="id_modulo_oculto">
                        
                        <div class="form-section">
                            <div class="input-group">
                                <label>ID Módulo:</label>
                                <input type="text" name="txt_id_modulo" id="txt_id_modulo" required>
                            </div>
                            <div class="input-group">
                                <label>Nombre:</label>
                                <input type="text" name="txt_nombre" id="txt_nombre" required>
                            </div>
                            <div class="input-group">
                                <label>Duración (ej. 120h):</label>
                                <input type="text" name="txt_duracion" id="txt_duracion">
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
                            <button type="submit" name="btn_guardar_modulo" id="btn_guardar_modulo">GUARDAR</button>
                            <button type="button" id="btn_cancelar_modulo">CANCELAR</button>
                        </div>
                    </form>
                </div>

                <div class="listado-container">
                    <table class="listado" id="modulos_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Duración</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <a href="frmUnidad_didactica.php">AÑADIR UNIDADES</a>
            </div>
        </div>
    </main>
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
    <script>
        // Lógica JavaScript
        const selectCarrera = document.getElementById('select_carrera');
        const modulosContainer = document.getElementById('modulos_container');
        const modulosTableBody = document.querySelector('#modulos_table tbody');
        const formModuloWrapper = document.getElementById('form_modulo_wrapper');
        const btnNuevoModulo = document.getElementById('btn_nuevo_modulo');
        const btnCancelarModulo = document.getElementById('btn_cancelar_modulo');
        const idCarreraOculto = document.getElementById('id_carrera_oculto');
        const idModuloOculto = document.getElementById('id_modulo_oculto');
        const nombreCarreraSpan = document.getElementById('nombre_carrera_seleccionada');

        selectCarrera.addEventListener('change', async (e) => {
            const carreraId = e.target.value;
            const carreraText = e.target.options[e.target.selectedIndex].text;
            
            modulosTableBody.innerHTML = '';
            formModuloWrapper.style.display = 'none';

            if (carreraId) {
                nombreCarreraSpan.textContent = carreraText;
                idCarreraOculto.value = carreraId;
                modulosContainer.style.display = 'block';
                await loadModulos(carreraId);
            } else {
                modulosContainer.style.display = 'none';
            }
        });

        btnNuevoModulo.addEventListener('click', () => {
            // Limpiar formulario y mostrar
            document.getElementById('form_modulo').reset();
            idModuloOculto.value = '';
            formModuloWrapper.style.display = 'block';
            document.getElementById('btn_guardar_modulo').textContent = 'GUARDAR';
        });

        btnCancelarModulo.addEventListener('click', () => {
            formModuloWrapper.style.display = 'none';
        });

        async function loadModulos(carreraId) {
            try {
                const response = await fetch(`/avance/HTML/DATOS/modulo_carrera.php?accion=listar&id_carrera=${carreraId}`);
                const modulos = await response.json();
                
                modulosTableBody.innerHTML = ''; // Limpiar la tabla
                
                if (modulos.length > 0) {
                    modulos.forEach(modulo => {
                        const row = modulosTableBody.insertRow();
                        row.innerHTML = `
                            <td>${modulo.ID_MODULO}</td>
                            <td>${modulo.NOMBRE}</td>
                            <td>${modulo.DURACION}</td>
                            <td>${modulo.INICIO}</td>
                            <td>${modulo.FIN}</td>
                            <td>
                                <button onclick="editarModulo('${modulo.ID_MODULO}', '${modulo.NOMBRE}', '${modulo.DURACION}', '${modulo.INICIO}', '${modulo.FIN}')">Editar</button>
                                <button onclick="eliminarModulo('${modulo.ID_MODULO}')">Eliminar</button>
                            </td>
                        `;
                    });
                } else {
                    modulosTableBody.innerHTML = '<tr><td colspan="6">No hay módulos registrados para esta carrera.</td></tr>';
                }
            } catch (error) {
                console.error('Error al cargar módulos:', error);
                modulosTableBody.innerHTML = '<tr><td colspan="6">Error al cargar los datos.</td></tr>';
            }
        }

        function editarModulo(id, nombre, duracion, inicio, fin) {
            document.getElementById('txt_id_modulo').value = id;
            document.getElementById('txt_nombre').value = nombre;
            document.getElementById('txt_duracion').value = duracion;
            document.getElementById('txt_inicio').value = inicio;
            document.getElementById('txt_fin').value = fin;
            idModuloOculto.value = id; // Para indicar que es una edición
            formModuloWrapper.style.display = 'block';
            document.getElementById('btn_guardar_modulo').textContent = 'ACTUALIZAR';
        }

        async function eliminarModulo(id) {
            if (confirm('¿Está seguro de que desea eliminar este módulo?')) {
                const formData = new FormData();
                formData.append('btn_eliminar_modulo', 'true');
                formData.append('id_modulo', id);
                
                try {
                    const response = await fetch('/avance/HTML/DATOS/modulo_carrera.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.text();
                    console.log(result);
                    // Recargar la tabla de módulos
                    const carreraId = document.getElementById('select_carrera').value;
                    if (carreraId) {
                        await loadModulos(carreraId);
                    }
                } catch (error) {
                    console.error('Error al eliminar el módulo:', error);
                    alert('Error al eliminar el módulo.');
                }
            }
        }
    </script>
</body>
</html>