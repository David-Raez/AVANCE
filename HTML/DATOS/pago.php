<?php
// /xampp/htdocs/avance/HTML/NEGOCIO/pago.php
ini_set('display_errors', 1);
session_start();
require_once '../conexion.php';

// Validar que la solicitud proviene de un formulario POST o GET con acción
$action = $_REQUEST['action'] ?? null;
$error_message = '';
$success_message = '';

if ($action) {
    switch ($action) {
        case 'create':
            // Lógica para registrar un nuevo tipo de pago
            $id_pago_post = $_POST['id_pago'] ?? '';
            $razon_pago_post = $_POST['razon_pago'] ?? '';
            $monto_post = $_POST['monto'] ?? '';

            if (empty($id_pago_post) || empty($razon_pago_post)) {
                $error_message = "Error: ID de Pago y Razón de Pago son obligatorios.";
            } else {
                $query = "INSERT INTO PAGO (ID_PAGO, RAZON_PAGO, MONTO) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($cn, $query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssds", $id_pago_post, $razon_pago_post, $monto_post);
                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Tipo de pago registrado exitosamente.";
                    } else {
                        $error_message = "Error al registrar: " . mysqli_error($cn);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            break;

        case 'update':
            // Lógica para actualizar un tipo de pago existente
            $id_pago_existente = $_POST['pago_id_existente'] ?? '';
            $id_pago_post = $_POST['id_pago'] ?? '';
            $razon_pago_post = $_POST['razon_pago'] ?? '';
            $monto_post = $_POST['monto'] ?? '';
            $estado_post = $_POST['estado'] ?? '1';

            if (empty($id_pago_existente) || empty($id_pago_post) || empty($razon_pago_post)) {
                $error_message = "Error: ID de Pago y Razón de Pago son obligatorios.";
            } else {
                $query = "UPDATE PAGO SET ID_PAGO = ?, RAZON_PAGO = ?, MONTO = ?, ESTADO = ? WHERE ID_PAGO = ?";
                $stmt = mysqli_prepare($cn, $query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssds", $id_pago_post, $razon_pago_post, $monto_post, $estado_post, $id_pago_existente);
                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Tipo de pago actualizado exitosamente.";
                    } else {
                        $error_message = "Error al actualizar: " . mysqli_error($cn);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            break;

        case 'delete':
            // Lógica para eliminar un tipo de pago
            $pago_id = $_GET['id'] ?? null;
            if (empty($pago_id)) {
                $error_message = "Error: ID de pago no especificado.";
            } else {
                $query = "DELETE FROM PAGO WHERE ID_PAGO = ?";
                $stmt = mysqli_prepare($cn, $query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $pago_id);
                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Tipo de pago eliminado exitosamente.";
                    } else {
                        $error_message = "Error al eliminar: " . mysqli_error($cn);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            break;

        default:
            $error_message = "Acción no válida.";
            break;
    }
}

// Redirigir de vuelta al formulario con los mensajes
header("Location: frmPago.php?error=$error_message&success=$success_message");
exit();

?>