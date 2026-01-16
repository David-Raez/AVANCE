<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmRecibo.php
ini_set('display_errors', 1);
require_once '../conexion.php';
include("../logeo/encabezado.php");

// El archivo recibo.php debe estar en la misma carpeta o incluirse correctamente
include '/xampp/htdocs/avance/HTML/DATOS/recibo.php';


$doc = $_POST['txt_buscar'] ?? null;
$rs = null; // Inicializamos la variable para evitar errores

// El bloque de procesamiento y la tabla se ejecutan solo en POST
if (isset($_POST['btn_buscar'])) {
    $rs = mysqli_query($cn, "SELECT * FROM MATRICULA WHERE N_DOCUMENTO_ALUMNO = '$doc'");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recibos y Pagos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/avance/HTML/PRESENTACION/estilo.css">
    <style>
        /* Variables y estilos base */
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
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        h1, h2 {
            text-align: center;
            color: var(--primary-color);
        }
        h2 {
            margin-top: 2rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1rem;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--light-bg);
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        input[type="submit"] {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
            background-color: var(--primary-color);
            color: #fff;
        }
        input[type="submit"]:hover {
            background-color: #357ABD;
            transform: translateY(-2px);
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
        .radio-options {
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--dark-bg);
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .radio-options label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .radio-options input[type="radio"] {
            margin: 0;
            transform: scale(1.2);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>GESTIÓN DE RECIBOS Y PAGOS</h1>
        </header>
        <div class="container">
            <form action="" method="POST" class="form-horizontal">
                <div class="form-group">
                    <label for="txt_buscar">BUSCAR MATRÍCULA:</label>
                    <input type="text" name="txt_buscar" value="<?php echo htmlspecialchars($doc); ?>" placeholder="INGRESE DOCUMENTO DEL ALUMNO" required>
                </div>
                <div class="action-buttons">
                    <input type="submit" name="btn_buscar" value="BUSCAR">
                </div>
            </form>

            <hr>

            <?php if (isset($_POST['btn_buscar'])) { ?>
                <div class="listado-container">
                    <?php
                    if ($rs && mysqli_num_rows($rs) > 0) {
                        ?>
                        <table class="listado">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>ID MATRÍCULA</th>
                                    <th>USUARIO QUE REGISTRÓ</th>
                                    <th>Nº DOCUMENTO ALUMNO</th>
                                    <th>CARRERA</th>
                                    <th>MÓDULO OCUPACIONAL</th>
                                    <th>FORMACIÓN</th>
                                    <th>BECADO</th>
                                    <th>FECHA REGISTRO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($xxx = mysqli_fetch_array($rs)) { ?>
                                    <tr onclick="document.getElementById('id_matricula').value='<?php echo htmlspecialchars($xxx[0]); ?>';">
                                        <td><input type="radio" name="matricula_seleccionada" value="<?php echo htmlspecialchars($xxx[0]); ?>"></td>
                                        <td><?php echo htmlspecialchars($xxx[0]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[1]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[2]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[3]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[4]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[5]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[6]); ?></td>
                                        <td><?php echo htmlspecialchars($xxx[7]); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php
                    } else {
                        echo "<p>No se encontraron resultados para el documento: " . htmlspecialchars($doc) . "</p>";
                    }
                    ?>
                </div>
            <?php } ?>

            <form action="frmRecibo.php" method="POST" class="form-horizontal">
                <h2>REGISTRAR NUEVO RECIBO</h2>
                <div class="form-group">
                    <label for="numero_recibo">Número de Recibo:</label>
                    <input type="text" id="numero_recibo" name="numero_recibo" required>
                </div>
                <div class="form-group">
                    <label for="id_matricula">ID DE MATRÍCULA:</label>
                    <input type="text" id="id_matricula" name="id_matricula" readonly required>
                </div>
                <div class="form-group">
                    <label>Código de Pago:</label>
                    <div class="radio-options">
                        <label><input type="radio" name="codigo_pago" value="PAGO000" required> PAGO000 ANULADO</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO001" required> PAGO001 MATRÍCULA (MÓDULOS DE CAPACITACIÓN 300 HRS)</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO002" required> PAGO002 MATRÍCULA (MÓDULOS DE PLANES DE ESTUDIO 520 HRS)</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO003" required> PAGO003 MATRÍCULA (MÓDULOS DE U.D. 24 - 48 HRS)</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO004" required> PAGO004 MATRÍCULA (FORMACIÓN CONTINUA - 20 - 48 HRS)</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO005" required> PAGO005 DONACIÓN PARA BIENES Y SERVICIOS GENERALES</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO006" required> PAGO006 ALQUILER DE ESPACIO EXTERIOR</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO007" required> PAGO007 ALQUILER DE ESPACIO INTERIOR</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO008" required> PAGO008 EMISIÓN DE CERTIFICADO DE ESTUDIOS DE LOS PROGRAMAS DE ESTUDIOS</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO009" required> PAGO009 EMISIÓN DE TÍTULOS</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO010" required> PAGO010 OTORGAMIENTO DE CONSTANCIA DE SITUACIÓN ACADÉMICA</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO011" required> PAGO011 LICENCIA DE ESTUDIOS</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO012" required> PAGO012 REINCORPORACIÓN</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO013" required> PAGO013 EMISIÓN DE DUPLICADO DE CERTIFICADO DE FORMACIÓN CONTINUA</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO014" required> PAGO014 EMISIÓN DE DUPLICADO DE SÍLABO</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO015" required> PAGO015 EMISIÓN DE DUPLICADOS DE CERTIFICADO MODULAR DE PROGRAMAS DE ESTUDIO</label>
                        <label><input type="radio" name="codigo_pago" value="PAGO016" required> PAGO016 EMISIÓN DE DUPLICADOS DE TÍTULOS</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="monto_pagado">Monto Pagado:</label>
                    <input type="number" id="monto_pagado" name="monto_pagado" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="fecha_pago">Fecha de Pago:</label>
                    <input type="date" id="fecha_pago" name="fecha_pago">
                </div>
                <div class="action-buttons">
                    <input type="submit" name="btn_registrar" value="REGISTRAR PAGO">
                </div>
            </form>
        </div>
    </main>
    <footer>
        <?php include('../logeo/pie.php'); ?>
    </footer>
    <script>
        // Script para pasar el ID de la matrícula seleccionada al campo de texto
        document.addEventListener('DOMContentLoaded', () => {
            const radioButtons = document.querySelectorAll('input[name="matricula_seleccionada"]');
            const idMatriculaInput = document.getElementById('id_matricula');
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', (event) => {
                    idMatriculaInput.value = event.target.value;
                });
            });
            
            // También al hacer clic en la fila de la tabla
            const tableRows = document.querySelectorAll('.listado tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('click', () => {
                    const radioButton = row.querySelector('input[type="radio"]');
                    if (radioButton) {
                        radioButton.checked = true;
                        idMatriculaInput.value = radioButton.value;
                    }
                });
            });
        });
    </script>
</body>
</html>