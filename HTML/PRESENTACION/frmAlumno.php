<?php 
// /xampp/htdocs/CETPRO/HTML/PRESENTACION/frmAlumno.php

ini_set('display_errors', 1);
require_once '../conexion.php';
include("../logeo/encabezado.php");

// Variables de estado
$mostrar_form_nuevo = isset($_SESSION['mostrar_form_nuevo']) && $_SESSION['mostrar_form_nuevo'];
$mostrar_form_edicion = isset($_SESSION['alumno_encontrado']) && $_SESSION['alumno_encontrado'];
$datos_alumno = $_SESSION['alumno_data'] ?? [];
$listado_alumnos = $_SESSION['listado_alumnos'] ?? [];

// Limpiar mensajes y estados de la sesión después de mostrarlos
$message_success = $_SESSION['registration_success'] ?? '';
$message_error = $_SESSION['registration_error'] ?? '';
unset($_SESSION['registration_success']);
unset($_SESSION['registration_error']);
if ($mostrar_form_nuevo) {
    unset($_SESSION['mostrar_form_nuevo']);
}
if ($mostrar_form_edicion) {
    unset($_SESSION['alumno_encontrado']);
}
if (!empty($listado_alumnos)) {
    unset($_SESSION['listado_alumnos']);
}

// Lógica para obtener los departamentos (se usa en el formulario)
$departamentos = [];
try {
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
    <title>Gestión de Alumnos</title>
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

        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="date"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--light-bg);
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, select:focus, input[type="password"]:focus, input[type="date"]:focus {
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
        input[name="btn_buscar"] { background-color: var(--primary-color); color: white; }
        input[name="btn_buscar"]:hover { background-color: #3b74b8; }
        input[name="btn_nuevo"] { background-color: var(--secondary-color); color: white; }
        input[name="btn_nuevo"]:hover { background-color: #41b49e; }
        input[name="btn_listar"] { background-color: #888; color: white; }
        input[name="btn_listar"]:hover { background-color: #666; }

        .form-details-buttons {
            display: flex;
            gap: 1rem;
        }
        .form-details-buttons input[type="submit"] {
            flex: 1;
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
        .radio-vertical label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .radio-vertical input[type="radio"] {
            margin-right: 10px;
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
            <img src="/avance/HTML/images/student.png" alt="Imagen de Alumno">
            <h1>CONTROL DE ALUMNOS</h1>
        </header>

        <div class="container">
            <?php
            if (!empty($message_error)) {
                echo '<div class="message message-error">' . htmlspecialchars($message_error) . '</div>';
            }
            if (!empty($message_success)) {
                echo '<div class="message message-success">' . htmlspecialchars($message_success) . '</div>';
            }
            ?>
            
            <form method="post" action="/avance/HTML/DATOS/alumno.php">
                <div class="form-section">
                    <div class="input-group">
                        <label>SELECCIONE TIPO DE BÚSQUEDA</label>
                        <select name="selTipo_busqueda">
                            <option value="0">POR APELLIDOS Y NOMBRES</option>
                            <option value="1">POR DNI</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>INGRESE VALOR</label>
                        <input type="text" name="txt_valor" placeholder="INGRESE UN ALUMNO REGISTRADO">
                    </div>
                </div>
                <div class="action-buttons">
                    <input type="submit" name="btn_buscar" value="BUSCAR">
                    <input type="submit" name="btn_nuevo" value="NUEVO REGISTRO">
                    <input type="submit" name="btn_listar" value="LISTAR">
                </div>
            </form>
            
            <hr>

            <?php if ($mostrar_form_edicion || $mostrar_form_nuevo) { ?>
                <h3>Detalles del Alumno</h3>
                <form method="post" action="/avance/HTML/DATOS/alumno.php">
                    <?php if ($mostrar_form_edicion) { ?>
                        <input type="hidden" name="txt_documento_antiguo" value="<?php echo htmlspecialchars($datos_alumno['N_DOCUMENTO_ALUMNO'] ?? ''); ?>">
                    <?php } ?>
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label>ELEGIR TIPO DOCUMENTO:</label>
                            <select name="selTIPO">
                                <option value="DNI" <?php echo ($datos_alumno['TIPO_DOC'] ?? '') == 'DNI' ? 'selected' : ''; ?>>DNI</option>
                                <option value="CE" <?php echo ($datos_alumno['TIPO_DOC'] ?? '') == 'CE' ? 'selected' : ''; ?>>CARNET DE EXTRANJERIA</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>INGRESAR DOCUMENTO:</label>
                            <input type="text" name="txt_documento" value="<?php echo htmlspecialchars($datos_alumno['N_DOCUMENTO_ALUMNO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR APELLIDO PATERNO:</label>
                            <input type="text" name="txt_paterno" value="<?php echo htmlspecialchars($datos_alumno['APE_PATERNO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR APELLIDO MATERNO:</label>
                            <input type="text" name="txt_materno" value="<?php echo htmlspecialchars($datos_alumno['APE_MATERNO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESAR NOMBRES:</label>
                            <input type="text" name="txt_nombres" value="<?php echo htmlspecialchars($datos_alumno['NOMBRES'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>FECHA DE NACIMIENTO:</label>
                            <input type="date" name="date_nac" value="<?php echo htmlspecialchars($datos_alumno['FECHA_NACIMIENTO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>ESTADO CIVIL:</label>
                            <div class="radio-group">
                                <label><input type="radio" name="rdoCIVIL" value="Soltero" <?php echo ($datos_alumno['ESTADO_CIVIL'] ?? '') == 'Soltero' ? 'checked' : ''; ?>> Soltero/a</label>
                                <label><input type="radio" name="rdoCIVIL" value="Casado" <?php echo ($datos_alumno['ESTADO_CIVIL'] ?? '') == 'Casado' ? 'checked' : ''; ?>> Casado/a</label>
                            </div>
                        </div>
                        <div class="input-group radio-vertical">
                            <label>MAXIMO GRADO DE INSTRUCCION CONCLUIDO:</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="SIN ESTUDIOS" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'SIN ESTUDIOS' ? 'checked' : ''; ?>> SIN ESTUDIOS</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="PRIMARIA COMPLETA EBR" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'PRIMARIA COMPLETA EBR' ? 'checked' : ''; ?>> PRIMARIA COMPLETA EBR</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="SECUNDARIA COMPLETA EBR" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'SECUNDARIA COMPLETA EBR' ? 'checked' : ''; ?>> SECUNDARIA COMPLETA EBR</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="TÉCNICO SUPERIOR COMPLETA" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'TÉCNICO SUPERIOR COMPLETA' ? 'checked' : ''; ?>> TÉCNICO SUPERIOR COMPLETA</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="BACHILLER" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'BACHILLER' ? 'checked' : ''; ?>> BACHILLER</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="LICENCIADO" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'LICENCIADO' ? 'checked' : ''; ?>> LICENCIADO</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="MAGISTER" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'MAGISTER' ? 'checked' : ''; ?>> MAGISTER</label>
                            <label><input type="radio" name="rd_grado_instruccion" value="DOCTOR" <?php echo ($datos_alumno['EDUCACION'] ?? '') == 'DOCTOR' ? 'checked' : ''; ?>> DOCTOR</label>
                        </div>
                        <div class="input-group">
                            <label>ELEGIR SEXO:</label>
                            <div class="radio-group">
                                <label><input type="radio" name="rdoSEXO" value="MASCULINO" <?php echo ($datos_alumno['SEXO'] ?? '') == 'MASCULINO' ? 'checked' : ''; ?>> MASCULINO</label>
                                <label><input type="radio" name="rdoSEXO" value="FEMENINO" <?php echo ($datos_alumno['SEXO'] ?? '') == 'FEMENINO' ? 'checked' : ''; ?>> FEMENINO</label>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>INGRESE EMAIL:</label>
                            <input type="email" name="txt_email" value="<?php echo htmlspecialchars($datos_alumno['EMAIL'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESE CELULAR:</label>
                            <input type="tel" name="txt_celular" value="<?php echo htmlspecialchars($datos_alumno['CELULAR'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESE CONADIS:</label>
                            <div class="radio-group">
                                <label><input type="radio" name="rdoCONADIS" value="Si" <?php echo ($datos_alumno['CONADIS'] ?? '') == 'Si' ? 'checked' : ''; ?>> Sí</label>
                                <label><input type="radio" name="rdoCONADIS" value="No" <?php echo ($datos_alumno['CONADIS'] ?? '') == 'No' ? 'checked' : ''; ?>> No</label>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>N° CARNET CONADIS:</label>
                            <input type="text" name="txt_n_conadis" value="<?php echo htmlspecialchars($datos_alumno['N_CARNET_CONADIS'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>N° RES. DIC. CONADIS:</label>
                            <input type="text" name="txt_rd_conadis" value="<?php echo htmlspecialchars($datos_alumno['N_RES_DIC_CONADIS'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>TIPO DE SEGURO:</label>
                            <input type="text" name="txt_seguro" value="<?php echo htmlspecialchars($datos_alumno['SEGURO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label>INGRESE OCUPACIÓN:</label>
                            <input type="text" name="txt_ocupacion" value="<?php echo htmlspecialchars($datos_alumno['OCUPACION'] ?? ''); ?>">
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
                                        <option value="<?php echo htmlspecialchars($dep); ?>" <?php echo ($datos_alumno['DEPARTAMENTO'] ?? '') == $dep ? 'selected' : ''; ?>><?php echo htmlspecialchars($dep); ?></option>
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
                                <select id="distrito" name="distrito_ubigeo" disabled>
                                    <option value="">Seleccione Distrito</option>
                                </select>
                            </div>
                        </div>
                    </div>
                                            
                    <div class="input-group">
                        <label>INGRESAR DIRECCIÓN:</label>
                        <input type="text" name="txt_direccion" value="<?php echo htmlspecialchars($datos_alumno['DIRECCION'] ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <h3>Lugar de Nacimiento</h3>
                    </div>
                    <div class="form-section">
                        <div class="input-group">
                            <label for="pais_nac">PAIS DE NACIMIENTO:</label>
                            <input type="text" name="txt_pais_nac" id="pais_nac" value="<?php echo htmlspecialchars($datos_alumno['PAIS_NACIMIENTO'] ?? 'PERÚ'); ?>">
                        </div>
                        <div class="input-group">
                            <label for="depar_nac">DEPARTAMENTO DE NACIMIENTO:</label>
                            <input type="text" name="txt_depar_nac" id="depar_nac" value="<?php echo htmlspecialchars($datos_alumno['DEPARTAMENTO_NACIMIENTO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label for="provin_nac">PROVINCIA DE NACIMIENTO:</label>
                            <input type="text" name="txt_provin_nac" id="provin_nac" value="<?php echo htmlspecialchars($datos_alumno['PROVINCIA_NACIMIENTO'] ?? ''); ?>">
                        </div>
                        <div class="input-group">
                            <label for="dis_nac">DISTRITO DE NACIMIENTO:</label>
                            <input type="text" name="txt_distrito_nac" id="dis_nac" value="<?php echo htmlspecialchars($datos_alumno['DISTRITO_NACIMIENTO'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="lugar_nac">LUGAR DE NACIMIENTO (exacto):</label>
                        <input type="text" name="txt_lugar_nac" id="lugar_nac" value="<?php echo htmlspecialchars($datos_alumno['LUGAR_NACIMIENTO'] ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <label>ESTADO DE ALUMNO:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="rd_estado" value="1" <?php echo ($datos_alumno['ESTADO'] ?? '') == '1' ? 'checked' : ''; ?>> ACTIVO</label>
                            <label><input type="radio" name="rd_estado" value="0" <?php echo ($datos_alumno['ESTADO'] ?? '') == '0' ? 'checked' : ''; ?>> INACTIVO</label>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>CLAVE:</label>
                        <input type="password" name="password" <?php echo $mostrar_form_nuevo ? 'required' : ''; ?> placeholder="Ingrese la clave">
                    </div>
                    <div class="input-group">
                        <label>CONFIRMAR CLAVE:</label>
                        <input type="password" name="confirm_password" <?php echo $mostrar_form_nuevo ? 'required' : ''; ?> placeholder="Confirme la clave">
                    </div>

                    <div class="form-details-buttons">
                        <?php if (!$mostrar_form_edicion) { ?>
                            <input type="submit" name="btn_guardar" value="GUARDAR">
                        <?php } else { ?>
                            <input type="submit" name="btn_borrar" value="ELIMINAR">
                            <input type="submit" name="btn_editar" value="ACTUALIZAR">
                        <?php } ?>
                    </div>
                </form>
            <?php } elseif (!empty($listado_alumnos)) { ?>
                <hr>
                <h3>Listado de Alumnos</h3>
                <div class="listado-container">
                    <form method="post" action="/avance/HTML/DATOS/alumno.php">
                        <table class="listado">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Documento</th>
                                    <th>Nombres Completos</th>
                                    <th>Sexo</th>
                                    <th>Email</th>
                                    <th>Celular</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listado_alumnos as $alumno) { ?>
                                    <tr>
                                        <td><input type="radio" name="rd_elegir" value="<?php echo htmlspecialchars($alumno['N_DOCUMENTO_ALUMNO']); ?>"></td>
                                        <td><?php echo htmlspecialchars($alumno['N_DOCUMENTO_ALUMNO'] ?? ''); ?></td>
                                        <td><?php echo $alumno['NOMBRE_COMPLETO']; ?></td>
                                        <td><?php echo htmlspecialchars($alumno['SEXO'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($alumno['EMAIL'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($alumno['CELULAR'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($alumno['ESTADO'] == '1' ? 'ACTIVO' : 'INACTIVO'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="action-buttons">
                            <input type="submit" name="btn_borrar" value="BORRAR">
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
        // Obtener los elementos select del formulario
        const departamentoSelect = document.getElementById('departamento');
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');

        // Función para limpiar y deshabilitar un select
        function resetSelect(selectElement, defaultOptionText) {
            selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
            selectElement.disabled = true;
        }

        // Función para cargar las provincias mediante AJAX
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

        // Función para cargar los distritos mediante AJAX
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
                }
            }
        }

        // Eventos de cambio para la interacción del usuario
        departamentoSelect.addEventListener('change', () => {
            loadProvincias(departamentoSelect.value);
        });

        provinciaSelect.addEventListener('change', () => {
            loadDistritos(departamentoSelect.value, provinciaSelect.value);
        });

        // Lógica de precarga al iniciar la página (Modo Edición)
        const alumnoData = <?php echo json_encode($datos_alumno); ?>;
        if (Object.keys(alumnoData).length > 0) {
            const initialDepartamento = alumnoData.DEPARTAMENTO;
            const initialProvincia = alumnoData.PROVINCIA;
            const initialDistrito = alumnoData.ID_DISTRITO_UBIGEO;
            
            departamentoSelect.value = initialDepartamento;
            loadProvincias(initialDepartamento).then(() => {
                provinciaSelect.value = initialProvincia;
                loadDistritos(initialDepartamento, initialProvincia).then(() => {
                    distritoSelect.value = initialDistrito;
                });
            });
        }
    </script>
</body>
</html>