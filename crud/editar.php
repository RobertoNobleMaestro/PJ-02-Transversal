<?php
require_once('../php/conexion.php');
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}
// Verificar si el SweetAlert ya se mostró
if (!isset($_SESSION['sweetalert_mostrado'])) {
    $_SESSION['sweetalert_mostrado'] = false;
}
// Verificar que se haya enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = htmlspecialchars($_POST['id_usuario']);
    $nombre_user = isset($_POST['nombre_user']) ? htmlspecialchars($_POST['nombre_user']) : '';
    $nombre_real = isset($_POST['nombre_real']) ? htmlspecialchars($_POST['nombre_real']) : '';
    $ape_usuario = isset($_POST['ape_usuario']) ? htmlspecialchars($_POST['ape_usuario']) : '';
    $rol_user = isset($_POST['rol_user']) ? htmlspecialchars($_POST['rol_user']) : '';

    try {
        // Construir la consulta SQL para actualizar
        $sql = "UPDATE tbl_usuarios 
                SET nombre_user = :nombre_user, 
                    nombre_real = :nombre_real, 
                    ape_usuario = :ape_usuario, 
                    rol_user = :rol_user 
                WHERE id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);

        // Enlazar parámetros
        $stmt->bindParam(':nombre_user', $nombre_user, PDO::PARAM_STR);
        $stmt->bindParam(':nombre_real', $nombre_real, PDO::PARAM_STR);
        $stmt->bindParam(':ape_usuario', $ape_usuario, PDO::PARAM_STR);
        $stmt->bindParam(':rol_user', $rol_user, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        // Ejecutar consulta
        $stmt->execute();

        // Redirigir a la lista de usuarios con un mensaje de éxito
        header('Location: ../menu-admin.php?mensaje=usuario_actualizado');
        exit();
    } catch (Exception $e) {
        echo "Error al actualizar el usuario: " . htmlspecialchars($e->getMessage());
        exit();
    }
} else {
    echo "Solicitud inválida.";
    exit();
}
?>
