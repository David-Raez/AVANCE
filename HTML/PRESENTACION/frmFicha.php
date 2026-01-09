<?php
require_once '/xampp/htdocs/avance/HTML/conexion.php';
include("/xampp/htdocs/avance/HTML/logeo/encabezado.php");

$doc = $_POST['txt_buscar'] ?? null;
$rs = null;

if (isset($_POST['btn_buscar'])) {
    $doc_sanitizado = mysqli_real_escape_string($cn, $doc);
    $rs = mysqli_query($cn, "SELECT * FROM MATRICULA WHERE N_DOCUMENTO_ALUMNO = '$doc_sanitizado'");
    if (!$rs) {
        echo "Error en la consulta: " . mysqli_error($cn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICHAS DE MATRÍCULA</title>
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
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--light-bg); color: var(--text-color); display: flex; flex-direction: column; min-height: 100vh; }
        .container { width: 90%; max-width: 900px; margin: 2rem auto; padding: 2rem; background-color: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); }
        h2 { color: var(--primary-color); font-weight: 600; text-align: center; margin-bottom: 2rem; }
        .form-horizontal { display: flex; flex-direction: column; gap: 1rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        label { font-weight: 600; color: var(--text-color); }
        input[type="text"] { padding: 12px; border: 1px solid var(--border-color); border-radius: var(--border-radius); background-color: var(--light-bg); transition: all 0.3s ease; width: 100%; box-sizing: border-box; }
        .action-buttons { text-align: center; margin-top: 1.5rem; }
        input[type="submit"] { padding: 12px 24px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; background-color: var(--primary-color); color: white; transition: background-color 0.3s ease; }
        input[type="submit"]:hover { background-color: #3b74b8; }
        .listado-container { margin-top: 2rem; overflow-x: auto; }
        .listado { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .listado th, .listado td { padding: 12px 15px; border: 1px solid var(--border-color); text-align: left; }
        .listado thead { background-color: var(--primary-color); color: white; }
        .listado tbody tr:nth-child(even) { background-color: var(--light-bg); }
        .listado tbody tr:hover { background-color: var(--dark-bg); }
        .no-results { text-align: center; color: #777; padding: 2rem; }
        select { padding: 10px; border: 1px solid var(--border-color); border-radius: var(--border-radius); background-color: var(--light-bg); width: 250px; margin-right: 15px; }
        .descargar-button { padding: 12px 24px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; background-color: var(--secondary-color); color: white; transition: background-color 0.3s ease; }
        .descargar-button:hover { background-color: #41b49e; }
        .form-buttons { display: flex; justify-content: flex-end; gap: 15px; align-items: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>BUSCAR FICHA DE MATRÍCULA</h2>
        <form action="" method="POST" class="form-horizontal">
            <div class="form-group">
                <label for="txt_buscar">BUSCAR POR DOCUMENTO DE ALUMNO:</label>
                <input type="text" name="txt_buscar" value="<?php echo htmlspecialchars($doc); ?>" placeholder="INGRESE DOCUMENTO DEL ALUMNO" required>
            </div>
            <div class="action-buttons">
                <input type="submit" name="btn_buscar" value="BUSCAR">
            </div>
        </form>

        <hr style="margin: 3rem 0; border: 0; border-top: 1px solid var(--border-color);">

        <?php if (isset($_POST['btn_buscar'])) { ?>
            <div class="listado-container">
                <?php
                if ($rs && mysqli_num_rows($rs) > 0) {
                ?>
                    <form action="../fichas/generar_fichas.php" method="POST">
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
                                    <tr>
                                        <td><input type="radio" name="id_matricula" value="<?php echo htmlspecialchars($xxx[0]); ?>" required></td>
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
                        <div class="form-buttons">
                            <label for="tipo_ficha">Seleccione el tipo de ficha a descargar:</label>
                            <select name="tipo_ficha" id="tipo_ficha" required>
                                <option value="">--Seleccione--</option>
                                <option value="carrera">Ficha de Carrera</option>
                                <option value="modulo">Ficha de Módulo</option>
                                <option value="formacion">Ficha de Formación Continua</option>
                            </select>
                            <input type="hidden" name="doc_alumno" value="<?php echo htmlspecialchars($doc); ?>">
                            <input type="submit" value="Descargar Ficha" class="descargar-button">
                        </div>
                    </form>
                <?php
                } else {
                    echo "<p class='no-results'>No se encontraron resultados para el documento: " . htmlspecialchars($doc) . "</p>";
                }
                ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>