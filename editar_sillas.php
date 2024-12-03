<?php
require_once('./php/conexion.php');
session_start();

if (isset($_POST['editar_sillas']) && isset($_POST['mesa_id']) && isset($_POST['numero_sillas'])) {
    $mesa_id = isset($_POST['mesa_id']) ? htmlspecialchars($_POST['mesa_id']) : '';
    $numero_sillas = isset($_POST['numero_sillas']) ? htmlspecialchars($_POST['numero_sillas']) : '';

    if (!is_numeric($numero_sillas) || $numero_sillas <= 0) {
        header("Location: ../menu.php?error=numero_sillas_invalido");
        exit();
    }

    try {   
        // Preparar la consulta SQL para actualizar el número de sillas
        $sql = "UPDATE tbl_mesas SET numero_sillas = :numero_sillas WHERE id_mesa = :mesa_id";
        $stmt = $conexion->prepare($sql);
        
        // Vincular los parámetros
        $stmt->bindParam(':numero_sillas', $numero_sillas, PDO::PARAM_INT);
        $stmt->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $categoria = isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : '';
            $id_sala = isset($_GET['id_sala']) ? intval($_GET['id_sala']) : 0;

            // Redirigir a la página de gestión de mesas con un mensaje de éxito
            header("Location: ./gestionar_mesas.php?categoria=" . $categoria . "&id_sala=" . $id_sala . "&success=sillas_actualizadas");
        } else {
            // Si la actualización no fue exitosa
            header("Location: ../menu.php?error=error_actualizando_sillas");
        }

    } catch (PDOException $e) {
        // Capturar cualquier error de PDO
        header("Location: ../menu.php?error=error_actualizando_sillas");
    } finally {
        // Cerrar la conexión
        $conexion = null;
    }
} else {
    header("Location: ../menu.php?error=campos_vacios");
}
?>
