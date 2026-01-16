<?php
// /xampp/htdocs/avance/HTML/PRESENTACION/frmPago.php
ini_set('display_errors', 1);

require_once '../conexion.php';
include("../logeo/encabezado.php");

// Definir variables de acción y datos del formulario
$action = $_GET['action'] ?? 'list';
$pago_id = $_GET['id'] ?? null;
$error_message = '';
$success_message = '';

// Variables para pre-llenar el formulario en modo edición
$id_pago = '';
$razon_pago = '';
$monto = '';
$estado = '';

// Lógica para procesar la acción de editar
if ($action == 'edit' && $pago_id) {
    $query = "SELECT ID_PAGO, RAZON_PAGO, MONTO, ESTADO FROM PAGO WHERE ID_PAGO = ?";
    $stmt = mysqli_prepare($cn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $pago_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($pago_data = mysqli_fetch_assoc($result)) {
            $id_pago = htmlspecialchars($pago_data['ID_PAGO']);
            $razon_pago = htmlspecialchars($pago_data['RAZON_PAGO']);
            $monto = htmlspecialchars($pago_data['MONTO']);
            $estado = htmlspecialchars($pago_data['ESTADO']);
        } else {
            $error_message = "No se encontró el tipo de pago para editar.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Tipos de Pago</title>
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
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--text-color);
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
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--light-bg);
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus {
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
        .action-buttons input[type="submit"] {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
            background-color: var(--primary-color);
            color: #fff;
        }
        .action-buttons input[type="submit"]:hover {
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
        .btn-edit, .btn-delete {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-edit { background-color: #28a745; }
        .btn-delete { background-color: #dc3545; }
        .btn-edit:hover { background-color: #218838; }
        .btn-delete:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <main class="cuerpo">
        <header>
            <h1>MANTENEDOR DE TIPOS DE PAGO</h1>
        </header>
        <div class="container">
            <?php
            if ($error_message) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            if ($success_message) {
                echo "<p style='color: green;'>$success_message</p>";
            }
            ?>

            <h2><?php echo ($action == 'edit' ? 'Editar Tipo de Pago' : 'Registrar Nuevo Tipo de Pago'); ?></h2>
            <form action="frmPago.php" method="POST">
                <input type="hidden" name="action" value="<?php echo ($action == 'edit' ? 'update' : 'create'); ?>">
                <input type="hidden" name="pago_id_existente" value="<?php echo htmlspecialchars($id_pago); ?>">

                <div class="form-group">
                    <label for="id_pago">ID de Pago:</label>
                    <input type="text" id="id_pago" name="id_pago" value="<?php echo htmlspecialchars($id_pago); ?>" required <?php echo ($action == 'edit' ? 'readonly' : ''); ?>>
                </div>

                <div class="form-group">
                    <label for="razon_pago">Razón de Pago:</label>
                    <input type="text" id="razon_pago" name="razon_pago" value="<?php echo htmlspecialchars($razon_pago); ?>" required>
                </div>

                <div class="form-group">
                    <label for="monto">Monto:</label>
                    <input type="number" id="monto" name="monto" value="<?php echo htmlspecialchars($monto); ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($estado); ?>">
                </div>

                <div class="action-buttons">
                    <?php if ($action == 'edit') { ?>
                        <input type="submit" name="btn_actualizar" value="ACTUALIZAR" class="campo-input">
                    <?php } else { ?>
                        <input type="submit" name="btn_registrar" value="REGISTRAR" class="campo-input">
                    <?php } ?>
                </div>
            </form>

            <hr>

            <h2>Listado de Tipos de Pago</h2>
            <div class="listado-container">
                <table class="listado">
                    <thead>
                        <tr>
                            <th>ID Pago</th>
                            <th>Razón de Pago</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pagos_rs = mysqli_query($cn, "SELECT ID_PAGO, RAZON_PAGO, MONTO, ESTADO FROM PAGO WHERE ESTADO = 1");
                        if (mysqli_num_rows($pagos_rs) > 0) {
                            while ($row = mysqli_fetch_assoc($pagos_rs)) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['ID_PAGO']); ?></td>
                                    <td><?php echo htmlspecialchars($row['RAZON_PAGO']); ?></td>
                                    <td><?php echo htmlspecialchars($row['MONTO']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ESTADO']); ?></td>
                                    <td>
                                        <a href="frmPago.php?action=edit&id=<?php echo urlencode($row['ID_PAGO']); ?>" class="btn-edit">Editar</a>
                                        <a href="pago.php?action=delete&id=<?php echo urlencode($row['ID_PAGO']); ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este tipo de pago?');">Eliminar</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay tipos de pago registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer>
        <?php include('../logeo/pie.php'); ?>
    </footer>
</body>
</html>