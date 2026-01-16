<?php
session_start();
require_once '../conexion.php';

$old = $_POST['txt_old'];
$new = $_POST['txt_new'];
$userId = $_SESSION['user_id'];

// 1. Verificar que la clave actual sea correcta
$sqlCheck = "SELECT CLAVE FROM usuario WHERE ID_USUARIO = '$userId'";
$res = mysqli_query($cn, $sqlCheck);
$row = mysqli_fetch_assoc($res);

if ($row['CLAVE'] === $old) {
    // 2. Si es correcta, actualizar
    $sqlUpdate = "UPDATE usuario SET CLAVE = '$new' WHERE ID_USUARIO = '$userId'";
    if (mysqli_query($cn, $sqlUpdate)) {
        echo 1; // Éxito
    } else {
        echo "error_db";
    }
} else {
    echo "error_pass"; // Clave actual no coincide
}
?>