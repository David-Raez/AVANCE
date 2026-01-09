<?php
// Incluye tu archivo de conexión y la librería TCPDF
require_once(__DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once(__DIR__ . '/../conexion.php');

// 1. Recibir y validar los datos
$id_matricula = $_POST['id_matricula'] ?? null;
$tipo_ficha = $_POST['tipo_ficha'] ?? null;
$doc_alumno = $_POST['doc_alumno'] ?? null;

if (!$id_matricula || !$tipo_ficha || !$doc_alumno) {
    die("Error: Faltan datos para generar la ficha.");
}
// 4. Generar el contenido HTML de la ficha según el tipo seleccionado
ob_start();

if ($tipo_ficha == 'carrera') {
    // HTML para Ficha de Carrera
    // 2. Obtener datos de la base de datos
    $query = "SELECT m.*, 
                a.*, 
                mc.*
            FROM MATRICULA m
            JOIN ALUMNO a ON m.N_DOCUMENTO_ALUMNO = a.N_DOCUMENTO_ALUMNO
            JOIN MODULO mc ON m.ID_MODULO = mc.ID_MODULO
            WHERE m.ID_MATRICULA = '$id_matricula'";

        $resultado = mysqli_query($cn, $query);
        if (!$resultado || mysqli_num_rows($resultado) == 0) {
            die("Error: No se encontraron datos para la matrícula.");
        }
        $carrera = mysqli_fetch_assoc($resultado);

        // 3. Obtener las unidades didácticas del módulo (solo si es necesario)
        $unidades_didacticas = [];
        if (($tipo_ficha == 'carrera' || $tipo_ficha == 'modulo') && !empty($carrera['ID_MODULO'])) {
            $id_modulo = mysqli_real_escape_string($cn, $carrera['ID_MODULO']);
            $query_unidades = "SELECT * FROM UNIDAD_DIDACTICA WHERE ID_MODULO = '$id_modulo'";
            $res_unidades = mysqli_query($cn, $query_unidades);
            if ($res_unidades) {
                while ($fila = mysqli_fetch_assoc($res_unidades)) {
                    $unidades_didacticas[] = $fila;
                }
            }
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ficha de Carrera</title>
    <style>
        /* 
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 8.5px; /* Reducido para que todo quepa 
        }
        */
        table {
            border-collapse: collapse;
            width: 100%;
            
        }
        th, td {
            border: 1px solid black;
            padding: 2px 3px; /* Padding reducido */
            text-align: left;
            font-size: 8.5px; /* Reducido para que todo quepa */
            vertical-align: top; /* Alinear el contenido a la parte superior */
        }
        th {
            background-color: gray; /* Usando nombre de color */
            font-size: 9.5px; /* Un poco más grande para encabezados */
            font-weight: bold;
        }
        h1 {
            font-size: 14px; /* Tamaño de título ajustado */
            margin: 0 0 2px 0; /* Margen de título ajustado */
            padding: 0;
            line-height: 1.1; /* Ajustar el espacio entre líneas */
        }
        strong {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .no-border {
            border: none !important;
        }
        .signature-line {
            border-top: 1px solid black;
            width: 70%; /* Ancho de la línea de firma */
            margin: 10px auto 0 auto; width: 80px; /* Tamaño de firma ajustado */
            height: auto;
            display: block;
            margin: 5px auto 0 auto;
        }
        .signature-text {
            font-size: 7.5px; /* Tamaño de texto de firma reducido */
            text-align: center;
            margin-top: 2px;
        }
        img.logo {
            width: 120px; /* Tamaño de logo ajustado */
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Ajustes específicos para las celdas de la tabla de datos principales */
        .main-data-table th { width: 22%; } /* Ancho de las columnas de encabezado */
        .main-data-table td { width: 28%; } /* Ancho de las columnas de datos */
        .main-data-table th:nth-child(3) { width: 22%; }
        .main-data-table td:nth-child(4) { width: 28%; }

        /* Ajuste para la fila de APELLIDOS Y NOMBRES */
        .full-width-cell { width: 78%; } /* 22% (th) + 78% (td colspan 3) = 100% */
        .no-border {
            border: none;
        }
        .pie {
            align-content: center;
            text-align: center;
            width: 230px;
            height: auto;
        }
    </style>
</head>
<body>
    <table class="main-header-table">
        <tr>
            <td style="width: 25%; text-align: center;;">
                <img src="http://localhost/avance/HTML/images/minedu.png" alt="minedu" class="logo">
            </td>
            <td style="width: 75%; text-align: center;">
                <h1 style="font-weight: bold;">FICHA DE REGISTRO DE MATRICULA</h1>
                <h1 style="font-weight: bold;">AÑO 2025</h1>
            </td>
        </tr>
    </table>

    <table class="main-data-table">
        <tr>
            <th style="width: 25%; background-color: gray;">NOMBRE DEL CETPRO</th>
            <td style="width: 30%; " colspan="1">LA CASA DEL NIÑO TRABAJADOR SAN MARTIN DE PORRES</td>
            <th style="width: 25%; background-color: gray;">DRE</th>
            <td style="width: 20%; " colspan="1">Lima Metropolitana</td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">CODIGO MODULAR</th>
            <td style="width: 30%;" colspan="1">1048958</td>
            <th style="width: 25%; background-color: gray;">TIPO DE GESTION</th>
            <td style="width: 20%;" colspan="1">Pública</td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">DEPARTAMENTO</th>
            <td style="width: 30%;" colspan="1">LIMA</td>
            <th style="width: 25%; background-color: gray;">PROVINCIA</th>
            <td style="width: 20%;" colspan="1">LIMA</td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">DISTRITO</th>
            <td style="width: 30%;" colspan="1">LIMA</td>
            <th style="width: 25%; background-color: gray;">TIPO DE GESTION</th>
            <td style="width: 20%;" colspan="1">Pública</td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">PROGRAMA DE ESTUDIOS</th>
            <td style="width: 30%;" colspan="1">PLAN DE ESTUDIO</td>
            <th style="width: 25%; background-color: gray;">PERIODO LECTIVO</th>
            <td style="width: 20%;" colspan="1"><?php echo htmlspecialchars($carrera['PERIODO_LECTIVO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">MODULO</th>
            <td style="width: 30%;" colspan="1"><?php echo htmlspecialchars($carrera['NOMBRE_MODULO_CARRERA'] ?? 'N/A'); ?></td>
            <th style="width: 25%; background-color: gray;">PERIODO DE CLASES</th>
            <td style="width: 20%;" colspan="1"><?php $periodo_clases = trim(($carrera['INICIO'] ?? '') . ' - ' . ($carrera['FIN'] ?? '')); echo htmlspecialchars($periodo_clases ?: ($carrera['PERIODO_DE_CLASES'] ??'N/A')); ?></td>
        </tr>
        <tr>
            <?php 
                $fecha = $carrera['INICIO'] ?? '';
                $año = substr($fecha, 0, 4);
                $mes = substr($fecha, 5, 2);
                $dia = substr($fecha, 8, 2);
                if ($año = 2025 && $mes >= '07') {
                    $año = '2025';
                    $ciclo = 'II';
                } else {
                    $ciclo = 'I';
                    $año = '2025';
                }
            ?>
            <th style="width: 25%; background-color: gray;">NIVEL FORMATIVO</th>
            <td style="width: 30%;" colspan="1">AUXILIAR TECNICO</td>
            <th style="width: 25%; background-color: gray;">PERIODO ACADEMICO</th>
            <td style="width: 20%;" colspan="1"><?php echo htmlspecialchars(($año." -" . " ".$ciclo) ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">TIPO DE PLAN DE ESTUDIOS</th>
            <td style="width: 30%;" colspan="1">PRESENCIAL</td>
            <th style="width: 25%;background-color: gray;">NUMERO DE DOCUMENTO</th>
            <td style="width: 20%;" colspan="1"><?php echo htmlspecialchars($carrera['N_DOCUMENTO_ALUMNO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">APELLIDOS Y NOMBRES</th>
            <td colspan="3" class="full-width-cell" style="width: 75%;">
                <?php $apellidos_nombres = trim(($carrera['APE_PATERNO'] ?? '') . ' ' . ($carrera['APE_MATERNO'] ?? '') . ', ' . ($carrera['NOMBRES'] ?? '')); echo htmlspecialchars($apellidos_nombres ?: ($carrera['NOMBRE_COMPLETO'] ?? 'N/A'));?>
            </td>
        </tr>
        <tr>
            <th  class="text-center" style="font-size: 11px; width: 100%; background-color: gray;">UNIDADES DIDACTICAS</th>
        </tr>
    </table>

    <table style="margin-top: 0;">
        <thead>
            <tr>
                <th style="width: 10%; background-color: gray;">N°</th>
                <th style="width: 45%; background-color: gray;">UNIDAD DIDACTICA</th>
                <th style="width: 15%; background-color: gray;">CRÉDITOS</th>
                <th style="width: 15%; background-color: gray;">HORAS</th>
                <th style="width: 15%; background-color: gray;">CONDICION</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($unidades_didacticas)) { ?>
                <?php $i = 1; ?>
                <?php foreach ($unidades_didacticas as $unidad) { ?>
                    <tr>
                        <td style="width: 10%; font-size: 10;"><?php echo $i++; ?></td>
                        <td style="width: 45%;"><?php echo htmlspecialchars($unidad['NOMBRE_UNIDAD'] ?? 'N/A'); ?></td>
                        <td style="width: 15%;"><?php echo htmlspecialchars($unidad['CREDITO'] ?? 'N/A'); ?></td>
                        <td style="width: 15%;"><?php echo htmlspecialchars($unidad['HORAS'] ?? 'N/A'); ?></td>
                        <td style="width: 15%;"><?php echo htmlspecialchars($unidad['CONDICION'] ?? 'N/A'); ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5" class="text-center">No se encontraron unidades didácticas para este módulo.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <table>
        <tfoot>
            <tr>
                <th colspan="5" class="text-center" style="background-color: gray;">UNIDADES DIDÁCTICAS DE SUBSANACIÓN</th>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td style="width: 45%;"></td>
                <td style="width: 15%;"></td>
                <td style="width: 15%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <img src="http://localhost/avance/HTML/images/FIRMA_ESTUDIANTE.png" alt="firma" class="pie">
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <img src="http://localhost/avance/HTML/images/FIRMA_SECRETARIA.png" alt="firma" class="pie">
                </td>
            </tr> 
        </tfoot>
    </table>
    
</body>
</html>
<?php
} else if ($tipo_ficha == 'modulo') {
    $query = "SELECT m.*,
                a.*,
                oc.*,
                distrito.NOMBRE_DISTRITO,
                prov.NOMBRE_PROVINCIA
            FROM MATRICULA m
            JOIN ALUMNO a ON m.N_DOCUMENTO_ALUMNO = a.N_DOCUMENTO_ALUMNO
            JOIN MODULO_OCUPACIONAL oc ON m.ID_MODULO_OCUPACIONAL = oc.ID_MODULO_OCUPACIONAL
            JOIN DISTRITO distrito ON a.ID_DISTRITO = distrito.ID_DISTRITO
            JOIN PROVINCIA prov ON distrito.ID_PROVINCIA = prov.ID_PROVINCIA
            WHERE m.ID_MATRICULA = '$id_matricula'";
                $resultado = mysqli_query($cn, $query);
                if (!$resultado || mysqli_num_rows($resultado) == 0) {
                    die("Error: No se encontraron datos para la matrícula.");
                }
                $datos = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ficha de Matrícula</title>
    <style>
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        th, td { border: 1px solid black; padding: 3px; text-align: left; word-wrap: break-word; font-size: 9px; }
        th { font-size: 9px; text-align: center; }
        .text-center { text-align: center; }
        .no-border { border: none; }
        .header-title { font-size: 15px; margin: 0; padding: 0; }
        .subtitle { font-size: 11px; }
        .signature-text { font-size: 8px; }
        .spacer { height: 5px; }
        img { max-width: 60px; height: auto; }
        .signature-img { max-width: 120px; height: auto; }
        .pie {
            align-content: center;
            text-align: center;
            width: 230px;
            height: auto;
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td class="no-border" style="width: 15%"><img src="http://localhost/avance/html/images/escudo.png" alt="minedu" style="width: 60px;"></td>
            <td colspan="5" class="no-border text-center" style="width: 85%">
                <p style="font-size: 15px; font-weight: bold; margin: 0; padding: 0;">MINISTERIO DE EDUCACIÓN</p>
                <p style="font-size: 9px; margin: 0;">EDUCACION TÉCNICO PRODUCTIVA</p>
                <p style="font-size: 15px; font-weight: bold; margin: 0;">FICHA DE MATRICULA</p>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="no-border" style="height: 5px;"></td>
        </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width: 70%;">
        <tr>
            <th class="text-center" style="width: 50%; background-color: gray">CODIGO DE INSCRIPCION</th>
            <td style="width: 50%; text-align:right"><?php echo "1048958002525"; ?></td>
        </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 5px;">
        <tr>
            <td colspan="2" class="no-border" style="height: 5px;"></td>
        </tr>
        <tr>
            <th style="width: 45%; background-color: gray;" colspan="3">1. NOMBRE DEL CETPRO</th>
            <th class="text-center" style="width: 30%; background-color: gray;" colspan="2">ESPECIALIDAD</th>
            <th class="text-center" style="width: 25%; background-color: gray;" colspan="2">TURNO</th>
        </tr>
        <tr>
            <td class="text-center" style="width: 45%;" colspan="3">LA CASA DEL NIÑO TRABAJADOR - SAN MARTIN DE PORRES</td>
            <td class="text-center" style="width: 30%;" colspan="2"><?php echo htmlspecialchars($datos['MODULO'] ?? 'N/A'); ?></td>
            <td class="text-center" style="width: 25%;" colspan="2"><?php echo htmlspecialchars($datos['TURNO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td colspan="6" style="height: 5px; width: 100%"></td>
        </tr>
        <tr>
            <th colspan="6" class="text-center" style="width: 100%; background-color: gray;">2. UBICACIÓN DEL CENTRO DE EDUCACIÓN TÉCNICO PRODUCTIVO</th>
        </tr>
        <tr>
            <td colspan="6" class="text-center subtitle" style="width: 100%; "><strong>Dirección Regional de Educación de Lima Metropolitana</strong></td>
        </tr>
        <tr>
            <td colspan="6" class="text-center subtitle"><strong>UGEL 03</strong></td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">PROVINCIA</th>
            <td style="width: 25%;">LIMA</td>
            <th style="width: 25%; background-color: gray;">DISTRITO</th>
            <td style="width: 25%;">LIMA</td>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">LUGAR</th>
            <td style="width: 25%;">LIMA</td>
            <th style="width: 25%; background-color: gray;">DIRECCION</th>
            <td style="width: 25%;">JR PUNO 412</td>
        </tr>
        <tr>
            <td colspan="6" style="height: 5px; width: 100%;"></td>
        </tr>
        <tr>
            <th colspan="5" style="width: 100%">3. DATOS PERSONALES DEL ESTUDIANTE</th>
        </tr>
        <tr>
            <th style="width: 25%; background-color: gray;">APELLIDOS PATERNO</th>
            <th style="width: 25%; background-color: gray;">APELLIDO MATERNO</th>
            <th style="width: 25%; background-color: gray;">NOMBRES</th>
            <th style="width: 25%; background-color: gray;" colspan="4">SEXO</th>
        </tr>
        <tr>
            <?php error_reporting(0); ?>
            <td class="text-center" style="width: 25%;"><?php echo htmlspecialchars($datos['APE_PATERNO'] ?? 'N/A'); ?></td>
            <td class="text-center" style="width: 25%;"><?php echo htmlspecialchars($datos['APE_MATERNO'] ?? 'N/A'); ?></td>
            <td class="text-center" style="width: 25%;"><?php echo htmlspecialchars($datos['NOMBRES'] ?? 'N/A'); ?></td>
            <th class="text-center" style="width: 6.25%; background-color: gray;">H</th>
            <td class="text-center" style="width: 6.25%;"><?php if ($datos['SEXO']== 'M'): echo "X"; endif;?></td>
            <th class="text-center" style="width: 6.25%; background-color: gray;">M</th>
            <td class="text-center" style="width: 6.25%;"><?php if ($datos['SEXO']== 'F'): echo "X"; endif; ?></td>
        </tr>
        <tr>
            <th class="text-center" colspan="4" style="width: 25%; background-color: gray;">NACIMIENTO</th>
            <th class="text-center" rowspan="2" style="width: 7.5%; background-color: gray;">LUGAR</th>
            <td class="text-center" rowspan="2" style="width: 17.5%"><?php echo htmlspecialchars($datos['LUGAR_NACIMIENTO'] ?? 'N/A'); ?></td>
            <th class="text-center" rowspan="2" style="width: 10%; background-color: gray;">DISTRITO</th>
            <td class="text-center" rowspan="2" style="width: 40%"><?php echo htmlspecialchars($datos['DISTRITO_NACIMIENTO'] ?? 'N/A'); ?></td>
        </tr>
        <tr style="width: 25%;">
            <th style="width: 7.5%;background-color: gray;">DIA</th>
            <th style="width: 7.5%;background-color: gray;">MES</th>
            <th style="width: 10%; background-color: gray;">AÑO</th>
        </tr>
        <?php 
            $fecha = $datos['FECHA_NACIMIENTO'] ?? '0000-00-00';
            $año = substr($fecha, 0, 4);
            $mes = substr($fecha, 5, 2);
            $dia = substr($fecha, 8, 2);
        ?>
        <tr>
            <td style="width: 7.5%"><?php echo htmlspecialchars($dia); ?></td>
            <td style="width: 7.5%"><?php echo htmlspecialchars($mes); ?></td>
            <td colspan="2" style="width: 10%"><?php echo htmlspecialchars($año); ?></td>
            <th class="text-center" style="height: 20px; background-color: gray; width: 7.5%">PAIS</th>
            <td class="text-center" style="width: 17.5  %"><?php echo htmlspecialchars($datos['PAIS_NACIMIENTO'] ?? 'N/A'); ?></td>
            <th class="text-center" style="background-color: gray; width: 10%">DPTO.</th>
            <td class="text-center" style="width:40%"><?php echo htmlspecialchars($datos['DEPARTAMENTO_NACIMIENTO'] ?? 'N/A'); ?></td>
        </tr>
        <?php 
            $fechaNacimiento = new DateTime($datos['FECHA_NACIMIENTO'] ?? '0000-00-00');
            $fechaActual = new DateTime();
            $edad = $fechaActual->diff($fechaNacimiento)->y;
        ?>
        <tr>
            <th class="text-center" colspan="2" style="background-color: gray; width: 15%;">EDAD</th>
            <td class="text-center" colspan="2" style="width: 10%"><?php echo htmlspecialchars($edad); ?></td>
            <th class="text-center" style="background-color: gray; width: 7.5%;">PROV.</th>
            <td class="text-center" colspan="3" style="width: 67.5% height: 15px"><?php echo htmlspecialchars($datos['PROVINCIA_NACIMIENTO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th class="text-center" colspan="4" style="background-color: gray;">ESTADO CIVIL</th>
            <th class="text-center" colspan="2" style="background-color: gray;">DOCUMENTO IDENTIDAD</th>
            <th class="text-center" style="background-color: gray;">GRADO DE INSTRUCCIÓN</th>
            <th class="text-center" style="background-color: gray;">OCUPACION</th>
        </tr>
        <tr>
            <td class="text-center" colspan="4"><?php echo htmlspecialchars($datos['ESTADO_CIVIL'] ?? 'N/A'); ?></td>
            <td class="text-center" colspan="2"><?php echo htmlspecialchars($datos['N_DOCUMENTO_ALUMNO'] ?? 'N/A'); ?></td>
            <td class="text-center"><?php echo htmlspecialchars($datos['EDUCACION'] ?? 'N/A'); ?></td>
            <td class="text-center"><?php echo htmlspecialchars($datos['OCUPACION'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="7" class="text-center" style="width: 100%; background-color: gray;">DOMICILIO</th>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">PROVINCIA</th>
            <td colspan="2" style="width: 30%;"><?php echo htmlspecialchars($datos['NOMBRE_PROVINCIA'] ?? 'N/A'); ?></td>
            <th style="width: 15%; background-color: gray;">DISTRITO</th>
            <td colspan="2" style="width: 40%;"><?php echo htmlspecialchars($datos['NOMBRE_DISTRITO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">LUGAR</th>
            <td colspan="2" style="width: 30%; "><?php echo htmlspecialchars($datos['NOMBRE_DISTRITO'] ?? 'N/A'); ?></td>
            <th style="width: 15%; background-color: gray;">CALLE</th>
            <td colspan="2" style="width: 40%;"><?php echo htmlspecialchars($datos['DIRECCION'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">TELEFONO</th>
            <td colspan="2" style="width: 30%;"><?php echo htmlspecialchars($datos['CELULAR'] ?? 'N/A'); ?></td>
            <th style="background-color: gray;">EMAIL</th>
            <td colspan="2" style="width: 40%;"><?php echo htmlspecialchars($datos['EMAIL'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td colspan="7" style="height: 5px; width: 100%"></td>
        </tr>
        <tr>
            <th colspan="7" style="width: 100%; background-color: gray;">4. DATOS DEL MODULO</th>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">CICLO</th>
            <td class="text-center" colspan="5" style="width: 85%;"><?php echo htmlspecialchars($datos['CICLO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">MODULO</th>
            <td class="text-center" colspan="5" style="width: 85%;"><?php echo htmlspecialchars($datos['MODULO'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="2" style="width: 15%; background-color: gray;">DURACION</th>
            <td class="text-center" style="width: 35%;"><?php echo htmlspecialchars($datos['DURACION'] ?? 'N/A'); ?></td>
            <th class="text-center" style="width: 10%; background-color: gray;">INICIO</th>
            <td class="text-center" style="width: 15%;"><?php echo htmlspecialchars($datos['INICIO'] ?? 'N/A'); ?></td>
            <th class="text-center" style="width: 10%; background-color: gray;">TERMINO</th>
            <td class="text-center" style="width: 15%;"><?php echo htmlspecialchars($datos['FIN'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <th colspan="3" style="width: 25%; background-color: gray;">REQUISITOS DE ACCESO</th>
            <td colspan="4" style="width: 75%; ">MAYOR DE 14 AÑOS, COPIA DE DNI LEGIBLE Y DEL APODERADO DE SER EL CASO</td>
        </tr>
        <tr>
            <th colspan="7" class="text-center" style="width: 100%; background-color: gray;">ENUMERAR LOS MÓDULOS APROBADOS Y CERTIFICADOS A LA FECHA</th>
        </tr>
        <tr>
            <th colspan="3" style="width: 20%; background-color: gray;">MÓDULOS</th>
            <td colspan="2" style="width: 40%;"><?php echo "" ?></td>
            <td colspan="2" style="width: 40%;"><?php echo "" ?></td>
        </tr>
        <tr>
            <th colspan="3" style="width: 20%; background-color: gray;">N° DE HORAS</th>
            <td colspan="2" style="width: 40%;"><?php echo "" ?></td>
            <td colspan="2" style="width: 40%;"><?php echo "" ?></td>
        </tr>
        <tr>
            <td colspan="7" class="no-border" style="height: 20px;"></td>
        </tr>
        <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <img src="http://localhost/avance/HTML/images/FIRMA_ESTUDIANTE.png" alt="firma" class="pie">
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <img src="http://localhost/avance/HTML/images/FIRMA_SECRETARIA.png" alt="firma" class="pie">
                </td>
            </tr>
    </table>
</body>
</html>
<?php
} else if ($tipo_ficha == 'formacion') {
    $query = "SELECT m.*,
                a.*,
                fc.*
                FROM MATRICULA m JOIN ALUMNO a ON m.N_DOCUMENTO_ALUMNO = a.N_DOCUMENTO_ALUMNO
                JOIN FORMACION_CONTINUA fc ON m.ID_FORM = fc.ID_FORM
                WHERE m.ID_MATRICULA = '$id_matricula'";
                $resultado = mysqli_query($cn, $query);
                if (!$resultado || mysqli_num_rows($resultado) == 0) {
                    die("Error: No se encontraron datos para la matrícula.");
                }
                $formacion = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ficha de Formación Continua</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: lightgray; }
    </style>
</head>
<body>
    <h1>FICHA DE REGISTRO DE MATRICULA - FORMACIÓN CONTINUA</h1>
    <h2>DATOS DEL ALUMNO</h2>
    <p>Nombre: <?php echo htmlspecialchars($formacion['NOMBRE_COMPLETO'] ?? 'N/A'); ?></p>
    <p>Documento: <?php echo htmlspecialchars($formacion['N_DOCUMENTO_ALUMNO'] ?? 'N/A'); ?></p>
    <h2>DETALLES DEL CURSO</h2>
    <p>Nombre del Curso: <?php echo htmlspecialchars($formacion['NOMBRE_FORMACION'] ?? 'N/A'); ?></p>
    <p>Período de Clases: <?php echo htmlspecialchars($formacion['PERIODO_CLASES'] ?? 'N/A'); ?></p>
</body>
</html>
<?php
}

// 5. Generar y forzar la descarga del PDF
$html = ob_get_clean();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Define la orientación del papel según el tipo de ficha
if ($tipo_ficha == 'carrera') {
    $pdf->setPageOrientation('L'); // Horizontal
} else {
    $pdf->setPageOrientation('P'); // Vertical por defecto (también para 'modulo' y 'formacion')
}

$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$filename = "Ficha_Matricula_" . $doc_alumno . ".pdf";
$pdf->Output($filename, 'D');
exit;
?>