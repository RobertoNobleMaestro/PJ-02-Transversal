<?php
require_once('../php/conexion.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
$id_mesa = htmlspecialchars($_POST['id_mesa']);
$id_sala = htmlspecialchars($_POST['id_sala']);
$eliminar_sala = htmlspecialchars($_POST['eliminar_sala']);

    try {
        // Desactivar autocommit para manejar transacciones manualmente
        $conexion->setAttribute(PDO::ATTR_AUTOCOMMIT, false);

        // Iniciar la transacción
        $conexion->beginTransaction();

        // Eliminar la mesa seleccionada
        $sql_delete_mesa = "DELETE FROM tbl_mesas WHERE id_mesa = :id_mesa";
        $stmt_delete_mesa = $conexion->prepare($sql_delete_mesa);
        $stmt_delete_mesa->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_delete_mesa->execute();

        // Si el usuario desea eliminar la sala, eliminamos todas las mesas de la sala y luego la sala
        if ($eliminar_sala == 'si') {
            // Eliminar todas las mesas de la sala
            $sql_delete_mesas = "DELETE FROM tbl_mesas WHERE id_sala = :id_sala";
            $stmt_delete_mesas = $conexion->prepare($sql_delete_mesas);
            $stmt_delete_mesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_delete_mesas->execute();

            // Eliminar la sala
            $sql_delete_sala = "DELETE FROM tbl_salas WHERE id_sala = :id_sala";
            $stmt_delete_sala = $conexion->prepare($sql_delete_sala);
            $stmt_delete_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_delete_sala->execute();
        }

        // Si todo ha ido bien, confirmamos la transacción
        $conexion->commit();

        // Redirigir al menú con mensaje de éxito
        header("Location: ../menu-recursos.php?success=recurso_eliminado");
        exit();
    } catch (Exception $e) {
        // Si ocurre un error, revertimos los cambios
        $conexion->rollBack();

        // Mostrar el mensaje de error
        echo "Error al eliminar los datos: " . htmlspecialchars($e->getMessage());
        die();
    }
}
?>
