<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmEditar_matricula.php
require_once '../conexion.php';
include("../logeo/encabezado.php");
// Verificar si llega el ID
if (!isset($_GET['id'])) {
    header("Location: frmGestion_matricula.php");
    exit();
}

$id_matricula = $_GET['id'];
$datos = null;

// 1. Obtener datos de la matrícula actual
$sql = "SELECT * FROM matricula WHERE ID_MATRICULA = ?";
$stmt = mysqli_prepare($cn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_matricula);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$datos = mysqli_fetch_assoc($result);

if (!$datos) {
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>Matrícula no encontrada. <a href='frmGestion_matricula.php'>Volver</a></div>";
    exit();
}

// 2. Obtener listas para los Selects
// A) LISTA DE ALUMNOS
$alumnos_query = mysqli_query($cn, "SELECT N_DOCUMENTO_ALUMNO, NOMBRES, APE_PATERNO, APE_MATERNO FROM alumno ORDER BY APE_PATERNO ASC");

// B) Programas
$carreras = mysqli_query($cn, "SELECT ID_CARRERA, PROGRAMA_ESTUDIO FROM carrera");
$mod_ocup = mysqli_query($cn, "SELECT ID_MODULO_OCUPACIONAL, MODULO FROM modulo_ocupacional");
$formaciones = mysqli_query($cn, "SELECT ID_FORM, NOMBRE_FORMACION FROM formacion_continua");

// Determinar qué tipo está seleccionado actualmente
$tipo_actual = 'none';
if (!empty($datos['ID_MODULO'])) $tipo_actual = 'carrera';
elseif (!empty($datos['ID_MODULO_OCUPACIONAL'])) $tipo_actual = 'modulo';
elseif (!empty($datos['ID_FORM'])) $tipo_actual = 'formacion';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Matrícula</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    
    <style>
        /* RESET Y ESTILOS GENERALES */
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif; /* Fuente moderna */
            background-color: #f4f7f6; /* Fondo gris muy suave */
            color: #333;
        }

        .cuerpo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 80vh;
            padding: 20px;
        }

        header h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        /* TARJETA DEL FORMULARIO */
        .form-card {
            background: #ffffff;
            width: 100%;
            max-width: 650px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); /* Sombra suave y elegante */
            border-top: 5px solid #3498db; /* Línea de color superior */
        }

        /* GRUPOS DE INPUTS */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 0.95rem;
        }

        /* ESTILOS DE INPUTS Y SELECTS */
        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 1rem;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background-color: #fafafa;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15); /* Resplandor azul */
        }

        /* SELECTS OCULTOS */
        .hidden { display: none; }

        /* CHECKBOX PERSONALIZADO */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-top: 10px;
            cursor: pointer;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.2);
            accent-color: #3498db; /* Color del check moderno */
        }
        
        .checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
            width: 100%;
        }

        /* BOTONES */
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-save {
            background-color: #27ae60; /* Verde elegante */
            color: white;
            box-shadow: 0 4px 6px rgba(39, 174, 96, 0.2);
        }

        .btn-save:hover {
            background-color: #2ecc71;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #e74c3c; /* Rojo suave */
            color: white;
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2);
        }

        .btn-cancel:hover {
            background-color: #ff6b6b;
            transform: translateY(-2px);
        }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            .form-card { padding: 25px; }
            .btn-container { flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>Editar Matrícula <span style="font-size:0.6em; color:#7f8c8d;">#<?php echo $id_matricula; ?></span></h1>
        </header>
        
        <div class="form-card">
            <form action="../DATOS/gestion_matricula.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?php echo $datos['ID_MATRICULA']; ?>">

                <div class="form-group">
                    <label for="alumno">Alumno Matriculado:</label>
                    <select name="n_documento_alumno" id="alumno" class="form-control" required>
                        <option value="">-- Buscar Alumno --</option>
                        <?php while($alum = mysqli_fetch_assoc($alumnos_query)): 
                            $selected = ($alum['N_DOCUMENTO_ALUMNO'] == $datos['N_DOCUMENTO_ALUMNO']) ? 'selected' : '';
                            $nombreCompleto = $alum['APE_PATERNO'] . ' ' . $alum['APE_MATERNO'] . ', ' . $alum['NOMBRES'];
                        ?>
                            <option value="<?php echo $alum['N_DOCUMENTO_ALUMNO']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($nombreCompleto . " (DNI: " . $alum['N_DOCUMENTO_ALUMNO'] . ")"); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_programa">Tipo de Programa:</label>
                    <select id="tipo_programa" name="tipo_programa" class="form-control" onchange="mostrarSelect()">
                        <option value="carrera" <?php echo ($tipo_actual == 'carrera') ? 'selected' : ''; ?>>Carrera Profesional</option>
                        <option value="modulo" <?php echo ($tipo_actual == 'modulo') ? 'selected' : ''; ?>>Módulo Ocupacional</option>
                        <option value="formacion" <?php echo ($tipo_actual == 'formacion') ? 'selected' : ''; ?>>Formación Continua</option>
                    </select>
                </div>

                <div class="form-group <?php echo ($tipo_actual != 'carrera') ? 'hidden' : ''; ?>" id="div_carrera">
                    <label>Carrera Profesional:</label>
                    <select name="id_carrera" class="form-control">
                        <option value="">-- Seleccione Carrera --</option>
                        <?php while($row = mysqli_fetch_assoc($carreras)): ?>
                            <option value="<?php echo $row['ID_CARRERA']; ?>" <?php echo ($datos['ID_MODULO'] == $row['ID_CARRERA']) ? 'selected' : ''; ?>>
                                <?php echo $row['PROGRAMA_ESTUDIO']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group <?php echo ($tipo_actual != 'modulo') ? 'hidden' : ''; ?>" id="div_modulo">
                    <label>Módulo Ocupacional:</label>
                    <select name="id_modulo_oc" class="form-control">
                        <option value="">-- Seleccione Módulo --</option>
                        <?php while($row = mysqli_fetch_assoc($mod_ocup)): ?>
                            <option value="<?php echo $row['ID_MODULO_OCUPACIONAL']; ?>" <?php echo ($datos['ID_MODULO_OCUPACIONAL'] == $row['ID_MODULO_OCUPACIONAL']) ? 'selected' : ''; ?>>
                                <?php echo $row['MODULO']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group <?php echo ($tipo_actual != 'formacion') ? 'hidden' : ''; ?>" id="div_formacion">
                    <label>Curso / Formación Continua:</label>
                    <select name="id_formacion" class="form-control">
                        <option value="">-- Seleccione Curso --</option>
                        <?php while($row = mysqli_fetch_assoc($formaciones)): ?>
                            <option value="<?php echo $row['ID_FORM']; ?>" <?php echo ($datos['ID_FORM'] == $row['ID_FORM']) ? 'selected' : ''; ?>>
                                <?php echo $row['NOMBRE_FORMACION']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="checkbox-wrapper">
                    <input type="checkbox" id="becado" name="becado" value="1" <?php echo ($datos['BECADO'] == 1) ? 'checked' : ''; ?>>
                    <label for="becado">Aplicar Beca Estudiantil</label>
                </div>

                <div class="btn-container">
                    <a href="frmGestion_matricula.php" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>
    
    <script>
        function mostrarSelect() {
            var tipo = document.getElementById('tipo_programa').value;
            
            // Ocultar todos con una animación suave (opcional, aquí solo clase css)
            document.getElementById('div_carrera').classList.add('hidden');
            document.getElementById('div_modulo').classList.add('hidden');
            document.getElementById('div_formacion').classList.add('hidden');

            // Mostrar el seleccionado
            if (tipo === 'carrera') {
                document.getElementById('div_carrera').classList.remove('hidden');
            } else if (tipo === 'modulo') {
                document.getElementById('div_modulo').classList.remove('hidden');
            } else if (tipo === 'formacion') {
                document.getElementById('div_formacion').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>