<?php 
require_once '../conexion.php';
include("../logeo/encabezado.php");

// Obtenemos datos frescos del usuario
$id = $_SESSION['user_id'];
$query = mysqli_query($cn, "SELECT * FROM usuario WHERE ID_USUARIO = '$id'");
$user = mysqli_fetch_assoc($query);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?php echo $user['NOMBRES']; ?>&size=128&background=random" class="rounded-circle shadow" alt="avatar">
                        <h2 class="mt-3 font-weight-bold"><?php echo $user['NOMBRES'] . " " . $user['APE_PATERNO']; ?></h2>
                        <span class="badge bg-info text-dark">Usuario Activo</span>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1">Documento de Identidad</p>
                            <h5><?php echo $user['N_DOCUMENTO_USUARIO']; ?></h5>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1">Fecha de Ingreso</p>
                            <h5><?php echo date('d/m/Y'); ?></h5>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <a href="frmCambiarClave.php" class="btn btn-warning btn-lg shadow-sm">
                            <i class="ti ti-lock-cog"></i> Configurar Seguridad y Contraseña
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../logeo/pie.php"); ?>