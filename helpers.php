<?php
// helpers.php
// Contiene funciones de utilidad, como la de obtener ítems de menú.

/**
 * Obtiene los ítems de menú a los que un rol específico tiene acceso.
 *
 * @param mysqli $cn Objeto de conexión a la base de datos.
 * @param int $role_id El ID del rol del usuario.
 * @return array Un array de arrays asociativos, donde cada sub-array es un ítem de menú (nombre_item, url_item).
 */
function getMenuItemsByRoleId($cn, $role_id) {
    $menu_items = [];
    $sql = "SELECT pm.nombre_item, pm.url_item
            FROM permisos_menu pm
            JOIN rol_x_menu_item rxm ON pm.id_menu_item = rxm.id_menu_item
            WHERE rxm.id_rol = ?
            ORDER BY pm.nombre_item ASC";

    $stmt = mysqli_prepare($cn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $role_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $menu_items[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        // En un entorno real, esto debería ir a un log de errores, no mostrarse al usuario.
        error_log("Error al preparar la consulta de menú: " . mysqli_error($cn));
    }
    return $menu_items;
}

/**
 * Verifica si el usuario actual tiene el rol requerido para una página.
 * @param mysqli $cn Objeto de conexión a la base de datos.
 * @param int $required_role_id El ID del rol requerido para acceder.
 * @return bool True si el usuario tiene el rol, false en caso contrario.
 */
function checkUserRole($cn, $required_role_id) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        return false; // No logueado
    }
    if (!isset($_SESSION['user_role_id'])) {
        return false; // Rol no definido
    }

    $user_role_id = $_SESSION['user_role_id'];

    // Para una verificación más flexible (ej. un admin puede acceder a todo),
    // podrías expandir esta lógica aquí. Por simplicidad, solo chequea igualdad.
    if ($user_role_id === $required_role_id) {
        return true;
    }

    // Opcional: Si quieres que el Administrador (ID_ROL = 1) pueda acceder a todas las páginas de rol específico
    if ($user_role_id == 1) { // Asumiendo que 1 es el ID del rol de Administrador
        return true;
    }

    return false;
}

?>