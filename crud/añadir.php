<?php
require_once('../php/conexion.php');
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_crear_usuario'])) {
    $nombre_user = htmlspecialchars($_POST['nombre_user']);
    $nombre_real = htmlspecialchars($_POST['nombre_real']);
    $ape_usuario = htmlspecialchars($_POST['ape_usuario']);
    $rol_user = intval($_POST['rol_user']);
    $password = $_POST['contrasena']; // Capturar la contraseña sin escapar todavía

    try {
        // Encriptar la contraseña con bcrypt
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Consulta para insertar el nuevo usuario
        $sql = "INSERT INTO tbl_usuarios (nombre_user, nombre_real, ape_usuario, rol_user, contrasena) 
                VALUES (:nombre_user, :nombre_real, :ape_usuario, :rol_user, :contrasena)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre_user', $nombre_user, PDO::PARAM_STR);
        $stmt->bindParam(':nombre_real', $nombre_real, PDO::PARAM_STR);
        $stmt->bindParam(':ape_usuario', $ape_usuario, PDO::PARAM_STR);
        $stmt->bindParam(':rol_user', $rol_user, PDO::PARAM_INT);
        $stmt->bindParam(':contrasena', $password_hash, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: ../menu-admin.php?mensaje=usuario_creado');
        exit();
    } catch (Exception $e) {
        echo "Error al añadir el usuario: " . htmlspecialchars($e->getMessage());
    }
}
?>Error al añadir el usuario: SQLSTATE[HY093]: Invalid parameter number: parameter was not defined