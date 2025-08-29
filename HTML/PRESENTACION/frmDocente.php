<?php
// /xampp/htdocs/CETPRO/HTML/PRESENTACION/frmDocente.php

require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");

// Obtener las variables de sesión para el estado de la interfaz
$docente_encontrado = $_SESSION['docente_encontrado'] ?? false;
$docente_data = $_SESSION['docente_data'] ?? [];
$listado_docentes = $_SESSION['listado_docentes'] ?? [];
$mostrar_form_nuevo = isset($_SESSION['mostrar_form_nuevo']) ? $_SESSION['mostrar_form_nuevo'] : false;

// Recupera los mensajes de sesión y los borra
$mensaje_error = $_SESSION['registration_error'] ?? '';
$mensaje_exito = $_SESSION['registration_success'] ?? '';
unset($_SESSION['registration_error']);
unset($_SESSION['registration_success']);
unset($_SESSION['docente_encontrado']);
unset($_SESSION['docente_data']);
unset($_SESSION['listado_docentes']);
unset($_SESSION['mostrar_form_nuevo']);

// Lógica para obtener los departamentos (se usa en el formulario)
$departamentos = [];
try {
    // Es importante asegurarse de que no haya resultados pendientes de consultas anteriores
    while (mysqli_more_results($cn) && mysqli_next_result($cn));
    $result = mysqli_query($cn, "SELECT DISTINCT NOMBRE_DEPARTAMENTO FROM departamento ORDER BY NOMBRE_DEPARTAMENTO");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $departamentos[] = $row['NOMBRE_DEPARTAMENTO'];
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener departamentos: " . mysqli_error($cn));
    }
} catch (Exception $e) {
    error_log("Excepción al obtener departamentos: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Docentes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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

        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--light-bg);
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, select:focus {
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

        .action-buttons input[type="submit"] {
            width: auto;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[name="btn_buscar"], input[name="btn_editar_seleccionado"] { background-color: var(--primary-color); color: white; }
        input[name="btn_buscar"]:hover, input[name="btn_editar_seleccionado"]:hover { background-color: #3b74b8; }
        input[name="btn_nuevo"] { background-color: var(--secondary-color); color: white; }
        input[name="btn_nuevo"]:hover { background-color: #41b49e; }
        input[name="btn_listar"] { background-color: #888; color: white; }
        input[name="btn_listar"]:hover { background-color: #666; }
        input[name="btn_guardar"] { background-color: var(--secondary-color); color: white; }
        input[name="btn_guardar"]:hover { background-color: #41b49e; }
        input[name="btn_borrar"], input[name="btn_eliminar_seleccionado"] { background-color: #e24a4a; color: white; }
        input[name="btn_borrar"]:hover, input[name="btn_eliminar_seleccionado"]:hover { background-color: #c93d3d; }
        input[name="btn_editar"] { background-color: var(--primary-color); color: white; }
        input[name="btn_editar"]:hover { background-color: #3b74b8; }

        .form-details-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .radio-group {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .radio-group label {
            margin-bottom: 0;
            font-weight: normal;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 5px;
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
        table.listado tbody td input[type="radio"] {
            transform: scale(1.2);
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            
            <img src="/avance/HTML/images/docente.png" alt="Imagen de Docente">
            <h1>CONTROL DE DOCENTES</h1>
        </header>

        <div class="container">
            <?php
            if (!empty($mensaje_error)) {
                echo '<div class="message message-error">' . htmlspecialchars($mensaje_error) . '</div>';
            }
            if (!empty($mensaje_exito)) {
                echo '<div class="message message-success">' . htmlspecialchars($mensaje_exito) . '</div>';
            }
            ?>
            
            <form method="post" action="/avance/HTML/DATOS/docente.php">
                <div class="form-section">
                    <div class="input-group">
                        <label>SELECCIONE TIPO DE BÚSQUEDA</label>
                        <select name="selTipo_busqueda">
                            <option value="0">POR NOMBRE DEL DOCENTE</option>
                            <option value="1">POR DNI DEL DOCENTE</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>INGRESE VALOR</label>
                        <input type="text" name="txt_valor" placeholder="INGRESA UN DOCENTE REGISTRADO">
                    </div>
                </div>
                <div class="action-buttons">
                    <input type="submit" name="btn_buscar" value="BUSCAR">
                    <input type="submit" name="btn_nuevo" value="NUEVO REGISTRO">
                    <input type="submit" name="btn_listar" value="LISTAR">
                </div>
            </form>
            
            <hr>

            <?php if ($docente_encontrado || $mostrar_form_nuevo) { ?>
                <h3>Detalles del Docente</h3>
                <form method="post" action="/avance/HTML/DATOS/docente.php">
                    <?php if ($docente_encontrado) { ?>
                        <input type="hidden" name="id_docente" value="<?php echo htmlspecialchars($docente_data['ID_DOCENTE'] ?? ''); ?>">
                        <input type="hidden" name="txt_documento_antiguo" value="<?php echo htmlspecialchars($docente_data['N_DOCUMENTO_DOCENTE'] ?? ''); ?>">
                    <?php } ?>
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label>ELEGIR TIPO DOCUMENTO:</label>
                            <select name="selTIPO">
                                <option value="DNI" <?php echo ($docente_data['TIPO_DOC'] ?? '') == 'DNI' ? 'selected' : ''; ?>>DNI</option>
                                <option value="CE" <?php echo ($docente_data['TIPO_DOC'] ?? '') == 'CE' ? 'selected' : ''; ?>>CARNET DE EXTRANJERIA</option>
                                <option value="CPP" <?php echo ($docente_data['TIPO_DOC'] ?? '') == 'CPP' ? 'selected' : ''; ?>>CARNET DE PERMISO TEMPORAL</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>INGRESAR DOCUMENTO:</label>
                            <input type="text" name="txt_documento" value="<?php echo htmlspecialchars($docente_data['N_DOCUMENTO_DOCENTE'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR APELLIDO PATERNO:</label>
                            <input type="text" name="txt_paterno" value="<?php echo htmlspecialchars($docente_data['APELLIDO_PATERNO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR APELLIDO MATERNO:</label>
                            <input type="text" name="txt_materno" value="<?php echo htmlspecialchars($docente_data['APELLIDO_MATERNO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR NOMBRES:</label>
                            <input type="text" name="txt_nombres" value="<?php echo htmlspecialchars($docente_data['NOMBRES'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>TIPO CONTRATO:</label>
                            <select name="selTIPO_CONTRATO">
                                <option value="NOMBRADO" <?php echo ($docente_data['TIPO_CONTRATO'] ?? '') == 'NOMBRADO' ? 'selected' : ''; ?>>NOMBRADO</option>
                                <option value="CONTRATADO" <?php echo ($docente_data['TIPO_CONTRATO'] ?? '') == 'CONTRATADO' ? 'selected' : ''; ?>>CONTRATADO</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>NÚMERO DE RESOLUCIÓN DIRECTORIAL:</label>
                            <input type="text" name="txt_rd" value="<?php echo htmlspecialchars($docente_data['N_RD'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label>ELEGIR SEXO:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="rdoSEXO" value="MASCULINO" <?php echo ($docente_data['SEXO'] ?? '') == 'MASCULINO' ? 'checked' : ''; ?>> MASCULINO</label>
                            <label><input type="radio" name="rdoSEXO" value="FEMENINO" <?php echo ($docente_data['SEXO'] ?? '') == 'FEMENINO' ? 'checked' : ''; ?>> FEMENINO</label>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label>INGRESAR EMAIL:</label>
                            <input type="email" name="txt_email" value="<?php echo htmlspecialchars($docente_data['EMAIL'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR CELULAR:</label>
                            <input type="tel" name="txt_celular" value="<?php echo htmlspecialchars($docente_data['CELULAR'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="location-section">
                        <h3>Ubicación del Domicilio</h3>
                        <div class="form-section">
                            <div class="input-group">
                                <label for="departamento">Departamento:</label>
                                <select id="departamento" name="departamento_domicilio">
                                    <option value="">Seleccione Departamento</option>
                                    <?php foreach ($departamentos as $dep) { ?>
                                        <option value="<?php echo htmlspecialchars($dep); ?>" <?php echo ($docente_data['NOMBRE_DEPARTAMENTO'] ?? '') == $dep ? 'selected' : ''; ?>><?php echo htmlspecialchars($dep); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="provincia">Provincia:</label>
                                <select id="provincia" name="provincia_domicilio" disabled>
                                    <option value="">Seleccione Provincia</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="distrito">Distrito:</label>
                                <select id="distrito" name="distrito" disabled>
                                    <option value="">Seleccione Distrito</option>
                                </select>
                            </div>
                        </div>
                        <div id="selectedLocation">
                            Ubicación seleccionada: <?php echo (!empty($docente_data['NOMBRE_DISTRITO'])) ? htmlspecialchars($docente_data['NOMBRE_DISTRITO']) : 'N/A'; ?>
                        </div>
                    </div>
                                
                    <div class="input-group">
                        <label>INGRESAR DIRECCIÓN:</label>
                        <input type="text" name="txt_direccion" value="<?php echo htmlspecialchars($docente_data['DIRECCION'] ?? ''); ?>">
                    </div>
                                
                    <div class="input-group">
                        <label>ESTADO CIVIL:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="rdoCIVIL" value="Soltero" <?php echo ($docente_data['ESTADO_CIVIL'] ?? '') == 'Soltero' ? 'checked' : ''; ?>> Soltero/a</label>
                            <label><input type="radio" name="rdoCIVIL" value="Casado" <?php echo ($docente_data['ESTADO_CIVIL'] ?? '') == 'Casado' ? 'checked' : ''; ?>> Casado/a</label>
                            <label><input type="radio" name="rdoCIVIL" value="Divorciado" <?php echo ($docente_data['ESTADO_CIVIL'] ?? '') == 'Divorciado' ? 'checked' : ''; ?>> Divorciado/a</label>
                            <label><input type="radio" name="rdoCIVIL" value="Viudo" <?php echo ($docente_data['ESTADO_CIVIL'] ?? '') == 'Viudo' ? 'checked' : ''; ?>> Viudo/a</label>
                        </div>
                    </div>
                                
                    <div class="input-group">
                        <label>TIPO DE SEGURO:</label>
                        <input type="text" name="txt_seguro" value="<?php echo htmlspecialchars($docente_data['SEGURO'] ?? ''); ?>">
                    </div>
                                
                    <div class="input-group">
                        <label>FECHA DE NACIMIENTO:</label>
                        <input type="date" name="date_nac" value="<?php echo htmlspecialchars($docente_data['FECHA_NACIMIENTO'] ?? ''); ?>">
                    </div>
                                
                    <div class="input-group">
                        <label>ESTADO DE DOCENTE:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="rd_estado" value="1" <?php echo ($docente_data['ESTADO'] ?? '') == '1' ? 'checked' : ''; ?>> ACTIVO</label>
                            <label><input type="radio" name="rd_estado" value="0" <?php echo ($docente_data['ESTADO'] ?? '') == '0' ? 'checked' : ''; ?>> INACTIVO</label>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label for="password_input">Contraseña:</label>
                            <input type="password" name="password" id="password_input" <?php echo !$docente_encontrado ? 'required' : ''; ?>>
                        </div>
                        <div class="input-group">
                            <label for="confirm_password_input">Confirmar Contraseña:</label>
                            <input type="password" name="confirm_password" id="confirm_password_input" <?php echo !$docente_encontrado ? 'required' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="form-details-buttons">
                        <?php if (!$docente_encontrado) { ?>
                            <input type="submit" name="btn_guardar" value="GUARDAR">
                        <?php } else { ?>
                            <input type="submit" name="btn_borrar" value="ELIMINAR">
                            <input type="submit" name="btn_editar" value="ACTUALIZAR">
                        <?php } ?>
                    </div>
                </form>
            <?php } elseif (!empty($listado_docentes)) { ?>
                <hr>
                <h3>Listado de Docentes</h3>
                <div class="listado-container">
                    <form method="post" action="/avance/HTML/DATOS/docente.php">
                        <table class="listado">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Tipo Doc.</th>
                                    <th>Documento</th>
                                    <th>Nombres</th>
                                    <th>Tipo Contrato</th>
                                    <th>RD</th>
                                    <th>Distrito</th>
                                    <th>Celular</th>
                                    <th>Sexo</th>
                                    <th>Fecha Nac.</th>
                                    <th>ID Usuario</th>
                                    <th>Estado</th>
                                    <th>Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listado_docentes as $xxx) { ?>
                                    <tr>
                                        <td><input type="radio" name="rd_elegir" value="<?php echo htmlspecialchars($alumno['N_DOCUMENTO_ALUMNO']); ?>"></td>
                                        <td><?php echo htmlspecialchars($xxx[1] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[2] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[3] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[4] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[5] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[6] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[7] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[8] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[9] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[10] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[11] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[12] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[13] ?? ''); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="form-details-buttons">
                            <input type="submit" name="btn_buscar" value="EDITAR" class="btn-verde">
                            <input type="submit" name="btn_borrar" value="ELIMINAR" class="btn-rojo">
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </main>
    <footer>
        <?php 
			require('../logeo/pie.php'); 
		?>
    </footer>
    <script>
        const departamentoSelect = document.getElementById('departamento');
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const selectedLocationDiv = document.getElementById('selectedLocation');

        function resetSelect(selectElement, defaultOptionText) {
            selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
            selectElement.disabled = true;
        }

        async function loadProvincias(departamento) {
            resetSelect(provinciaSelect, 'Seleccione Provincia');
            resetSelect(distritoSelect, 'Seleccione Distrito');
            if (departamento) {
                try {
                    const response = await fetch(`../ubicacion/get_provincias.php?departamento=${encodeURIComponent(departamento)}`);
                    const provincias = await response.json();
                    if (provincias.length > 0) {
                        provincias.forEach(prov => {
                            const option = document.createElement('option');
                            option.value = prov;
                            option.textContent = prov;
                            provinciaSelect.appendChild(option);
                        });
                        provinciaSelect.disabled = false;
                    }
                } catch (error) {
                    console.error('Error al cargar provincias:', error);
                }
            }
        }

        async function loadDistritos(departamento, provincia) {
            resetSelect(distritoSelect, 'Seleccione Distrito');
            if (departamento && provincia) {
                try {
                    const response = await fetch(`../ubicacion/get_distritos.php?departamento=${encodeURIComponent(departamento)}&provincia=${encodeURIComponent(provincia)}`);
                    const distritos = await response.json();
                    if (distritos.length > 0) {
                        distritos.forEach(dist => {
                            const option = document.createElement('option');
                            option.value = dist.ubigeo;
                            option.textContent = dist.nombre;
                            distritoSelect.appendChild(option);
                        });
                        distritoSelect.disabled = false;
                    }
                } catch (error) {
                    console.error('Error al cargar distritos:', error);
                }
            }
        }
        
        departamentoSelect.addEventListener('change', () => {
            loadProvincias(departamentoSelect.value);
            updateLocationDisplay();
        });

        provinciaSelect.addEventListener('change', () => {
            loadDistritos(departamentoSelect.value, provinciaSelect.value);
            updateLocationDisplay();
        });

        distritoSelect.addEventListener('change', () => {
            updateLocationDisplay();
        });

        function updateLocationDisplay() {
            const selectedDepartamento = departamentoSelect.options[departamentoSelect.selectedIndex]?.textContent || 'N/A';
            const selectedProvincia = provinciaSelect.options[provinciaSelect.selectedIndex]?.textContent || 'N/A';
            const selectedDistrito = distritoSelect.options[distritoSelect.selectedIndex]?.textContent || 'N/A';
            const selectedUbigeo = distritoSelect.value;
            
            if (selectedUbigeo) {
                selectedLocationDiv.textContent = `Ubicación seleccionada: ${selectedDepartamento} > ${selectedProvincia} > ${selectedDistrito} (Ubigeo: ${selectedUbigeo})`;
            } else {
                selectedLocationDiv.textContent = 'Ubicación seleccionada: N/A';
            }
        }
        
        const docenteData = <?php echo json_encode($docente_data); ?>;
        if (docenteData && docenteData.ID_DOCENTE) {
            const initialDepartamento = docenteData.NOMBRE_DEPARTAMENTO;
            const initialProvincia = docenteData.NOMBRE_PROVINCIA;
            const initialDistrito = docenteData.ID_DISTRITO_UBIGEO;
            
            departamentoSelect.value = initialDepartamento;
            loadProvincias(initialDepartamento).then(() => {
                provinciaSelect.value = initialProvincia;
                loadDistritos(initialDepartamento, initialProvincia).then(() => {
                    distritoSelect.value = initialDistrito;
                    updateLocationDisplay();
                });
            });
        }
    </script>
</body>
</html>