<?php
    
    ini_set('display_errors', 1);
    include '/xampp/htdocs/avance/HTML/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_registrar'])) {

  $recibo = $_POST['numero_recibo'];
  $id_matricula = $_POST['id_matricula'];
  $id_pago = $_POST['codigo_pago'];
  $monto = $_POST['monto_pagado'];
  $id_usuario = $_SESSION['user_id']; 
  $fecha = $_POST['fecha_pago'];
   if (is_null($id_usuario)) {
      // Maneja el error, por ejemplo, redirigiendo a la página de login
      $_SESSION['registration_error'] = "Debe iniciar sesión para realizar esta operación.";
      header("Location: /avance/HTML/PRESENTACION/frmLogin.php");
      exit();
  }

  $sql = "INSERT INTO RECIBO (ID_RECIBO,ID_PAGO,ID_MATRICULA,ID_USUARIO,MONTO,FECHA) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($cn,$sql);
  
  if ($stmt) {
        // 4. Vincular parámetros (¡Ajusta 's' por el tipo de dato de cada columna!)
        // 's' = string, 'i' = integer, 'd' = double, 'b' = blob
        mysqli_stmt_bind_param($stmt, "isiids", // 22 's' para 22 strings (ajusta según tus tipos reales)
            $recibo, $id_pago, $id_matricula, $id_usuario, $monto,$fecha
        );

        // 5. Ejecutar la sentencia preparada
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Registro guardado con éxito!');</script>";
        } else {
            echo "<script>alert('Error al guardar el registro: " . mysqli_stmt_error($stmt) . "');</script>";
            error_log("Error de inserción: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt); // Cerrar la sentencia
    } else {
        echo "<script>alert('Error en la preparación de la consulta: " . mysqli_error($cn) . "');</script>";
        error_log("Error al preparar la consulta: " . mysqli_error($cn));
    }
}
?>