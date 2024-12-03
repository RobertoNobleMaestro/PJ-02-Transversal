<?php
require_once('conexion.php');
session_start();

if (isset($_POST['btn_iniciar_sesion']) && !empty($_POST['Usuario']) && !empty($_POST['Contra'])) {
    $contra = isset($_POST['Contra']) ? htmlspecialchars($_POST['Contra']) : '';
    $usuario = isset($_POST['Usuario']) ? htmlspecialchars($_POST['Usuario']) : '';
    $_SESSION['usuario'] = $usuario;

    try {
        // Obtener el nombre de usuario, la contraseña y el rol
        $sql = "SELECT nombre_user, contrasena, rol_user FROM tbl_usuarios WHERE nombre_user = :usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();

        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_db) {
            if (password_verify($contra, $usuario_db['contrasena'])) {
                // Almacenar el nombre de usuario y el rol en la sesión
                $_SESSION['Usuario'] = $usuario;
                $_SESSION['rol_user'] = $usuario_db['rol_user']; // Almacenar el rol en la sesión

                // Redirigir dependiendo del rol
                if ($_SESSION['rol_user'] == 1) { // Ejemplo: rol 1 es "Camarero"
                    header("Location: ../menu.php");
                } elseif ($_SESSION['rol_user'] == 2) { // Ejemplo: rol 2 es "Administrador"
                    header("Location: ../menu-admin.php");
                } else {
                    // Si el rol no es ninguno de los anteriores, redirigir a una página predeterminada
                    header("Location: ../menu.php");
                }
                exit();
            } else {
                header('Location: ../index.php?error=contrasena_incorrecta');
            }
        } else {
            header('Location: ../index.php?error=usuario_no_encontrado');
        }

    } catch (Exception $e) {
        echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
    }
} else {
    header('Location: ../index.php?error=campos_vacios');
}
?>
