<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmCarrera.php


require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");

// Mensajes para el usuario
$mensaje_error = $_SESSION['carrera_error'] ?? '';
$mensaje_exito = $_SESSION['carrera_success'] ?? '';
$nueva_carrera_id = $_SESSION['nueva_carrera_id'] ?? null;

unset($_SESSION['carrera_error']);
unset($_SESSION['carrera_success']);
unset($_SESSION['nueva_carrera_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_guardar_carrera'])) {
    $nombre_carrera = $_POST['txt_nombre_carrera'];
    
    // Aquí puedes agregar validaciones de datos
    if (empty($nombre_carrera)) {
        $_SESSION['carrera_error'] = "El nombre de la carrera no puede estar vacío.";
        header("Location: frmCarrera.php");
        exit();
    }
    
    // Usar un procedimiento almacenado o una consulta directa para insertar la carrera
    $query = "INSERT INTO carrera (PROGRAMA_ESTUDIO) VALUES (?)";
    $stmt = mysqli_prepare($cn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nombre_carrera);
    
    if (mysqli_stmt_execute($stmt)) {
        // Obtener el ID de la última inserción
        $id_carrera = mysqli_insert_id($cn);
        
        // Almacenar el ID en la sesión para el siguiente formulario
        $_SESSION['carrera_success'] = "Carrera creada exitosamente. ¡Puedes agregarle módulos ahora!";
        $_SESSION['nueva_carrera_id'] = $id_carrera;
        header("Location: frmCarrera.php");
        exit();
    } else {
        $_SESSION['carrera_error'] = "Error al guardar la carrera: " . mysqli_error($cn);
        header("Location: frmCarrera.php");
        exit();
    }
}

// Obtener la lista de carreras existentes para mostrar en la tabla
$carreras = [];
try {
    // Asegurarse de que no haya resultados pendientes de consultas anteriores
    while (mysqli_more_results($cn) && mysqli_next_result($cn));

    $result = mysqli_query($cn, "SELECT ID_CARRERA, PROGRAMA_ESTUDIO FROM carrera ORDER BY PROGRAMA_ESTUDIO");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $carreras[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Exception $e) {
    // Manejo de errores
    error_log("Excepción al obtener la lista de carreras: " . $e->getMessage());
}

mysqli_close($cn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carreras</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/avance/HTML/PRESENTACION/estilo.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .container { max-width: 800px; margin: 50px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        h1 { color: #4a90e2; text-align: center; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .message-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: 600; display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-container { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none;  color: white; display: inline-block; }
        .btn-success { background-color: #50e3c2; }
        .btn-success:hover { background-color: #41b49e; }
        .btn-primary { background-color: #4a90e2; }
        .btn-primary:hover { background-color: #3b74b8; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; font-weight: 600; }
        .table-actions { text-align: center; }
        .btn-link { background: none; color: #4a90e2; border: none; padding: 0; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        
    </header>
    
    <div class="container">
        <h1>Gestión de Carreras</h1>

        <?php if ($mensaje_error): ?>
            <div class="message message-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
        <?php endif; ?>

        <?php if ($mensaje_exito): ?>
            <div class="message message-success">
                <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
                <?php if ($nueva_carrera_id): ?>
                    <div class="btn-container">
                        <a href="frmModulo_Carrera.php?id_carrera=<?php echo htmlspecialchars($nueva_carrera_id); ?>" class="btn btn-primary">
                            Ir a Módulos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h2>Carreras Existentes</h2>
        <?php if (count($carreras) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Carrera</th>
                        <th>Nombre de la Carrera</th>
                        <th class="table-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carreras as $carrera): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($carrera['ID_CARRERA']); ?></td>
                        <td><?php echo htmlspecialchars($carrera['PROGRAMA_ESTUDIO']); ?></td>
                        <td class="table-actions">
                            <a href="frmModulo_Carrera.php?id_carrera=<?php echo htmlspecialchars($carrera['ID_CARRERA']); ?>" class="btn-link">Agregar Módulos</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay carreras registradas.</p>
        <?php endif; ?>

        <hr>

        <h2>Crear Nueva Carrera</h2>
        <form method="post" action="frmCarrera.php">
            <div class="form-group">
                <label for="txt_nombre_carrera">Nombre de la Carrera:</label>
                <input type="text" id="txt_nombre_carrera" name="txt_nombre_carrera" required>
            </div>
            <div class="btn-container">
                <input type="submit" name="btn_guardar_carrera" value="Guardar Carrera" class="btn btn-success">
            </div>
        </form>
    </div>
    
    <footer>
        <?php require('../logeo/pie.php'); ?>
    </footer>
</body>
</html>