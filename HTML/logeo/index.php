<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>FUNCION REQUIRE</title>
	<link rel="stylesheet" type="text/css" href="estilos.css">
</head>
<body>
	<header>
		<?php 
			error_reporting(0);
			require('encabezado.php'); 
			require('capturar.php');
		?>
	</header>
	<section>
		<form method="post" action="index.php">
			<table border="2" cellspacing="0" cellpadding="5" width="500">
				<tr>
					<td>USUARIO</td>
					<td>
						<input type="text" name="txt_usuario">
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="txt_password" maxlength="10" ></td>
				</tr>
				<tr>
					<td colspan="2" id="centrado"><input type="submit" name="btn_ingresar" value="INGRESAR"></td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
							if (isset($_POST['btn_ingresar'])) {
								require('validar.php');
								if ($acceso=="ok") {
									header("location:bienvenido.php");
								}
								else {
									echo "ERROR DE DATOS";
								}
							}
						?>
					</td>
				</tr>
			</table>
		</form>
	</section>
	<footer>
		<?php 
			require('pie.php');
		?>
	</footer>
</body>
</html>