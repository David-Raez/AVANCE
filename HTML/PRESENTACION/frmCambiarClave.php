<?php 
require_once '../conexion.php';
include("../logeo/encabezado.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-dark text-white p-3">
                    <h5 class="mb-0"><i class="ti ti-shield-lock"></i> Cambiar Contraseña</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">Por seguridad, tu sesión se cerrará automáticamente al cambiar la clave.</p>
                    
                    <form id="formUpdatePass">
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" name="txt_old" id="txt_old" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" name="txt_new" id="txt_new" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="txt_confirm" required>
                        </div>

                        <div class="text-end">
                            <a href="frmPerfil.php" class="btn btn-light">Cancelar</a>
                            <button type="button" id="btnGuardarClave" class="btn btn-primary px-4">
                                Actualizar Ahora
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnGuardarClave').click(function() {
        const passNew = $('#txt_new').val();
        const passConfirm = $('#txt_confirm').val();

        if ($('#txt_old').val() == "" || passNew == "") {
            alertify.warning("Por favor rellene los campos");
            return;
        }

        if (passNew !== passConfirm) {
            alertify.error("Las contraseñas nuevas no coinciden");
            return;
        }

        let datos = $('#formUpdatePass').serialize();

        $.ajax({
            type: "POST",
            url: "CambiarClave.php", // Se conecta con tu archivo de lógica
            data: datos,
            success: function(r) {
                if (r == 1) {
                    alertify.success("Clave actualizada con éxito");
                    setTimeout(() => { window.location = "../logeo/logout.php"; }, 1500);
                } else if (r == "error_pass") {
                    alertify.error("La contraseña actual es incorrecta");
                } else {
                    alertify.error("Error en el sistema: " + r);
                }
            }
        });
    });
});
</script>

<?php include("../logeo/pie.php"); ?>