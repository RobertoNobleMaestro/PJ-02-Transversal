<?php
require_once('../php/conexion.php');
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
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

        // Si el usuario quiere eliminar solo la mesa
        if ($eliminar_sala == 'no') {
            // Eliminar primero las ocupaciones asociadas a la mesa
            $sql_delete_ocupacion = "DELETE FROM tbl_reservas WHERE id_mesa = :id_mesa";
            $stmt_delete_ocupacion = $conexion->prepare($sql_delete_ocupacion);
            $stmt_delete_ocupacion->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
            $stmt_delete_ocupacion->execute();

            // Eliminar las reservas de recursos asociadas a la mesa
            $sql_delete_reserva = "DELETE FROM tbl_reservas WHERE id_mesa = :id_mesa";
            $stmt_delete_reserva = $conexion->prepare($sql_delete_reserva);
            $stmt_delete_reserva->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
            $stmt_delete_reserva->execute();

            // Eliminar la mesa
            $sql_delete_mesa = "DELETE FROM tbl_mesas WHERE id_mesa = :id_mesa";
            $stmt_delete_mesa = $conexion->prepare($sql_delete_mesa);
            $stmt_delete_mesa->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
            $stmt_delete_mesa->execute();
        } else {
            // Si el usuario quiere eliminar también la sala

            // Eliminar primero las ocupaciones asociadas a todas las mesas de la sala
            $sql_delete_ocupaciones = "DELETE FROM tbl_reservas WHERE id_mesa IN (SELECT id_mesa FROM tbl_mesas WHERE id_sala = :id_sala)";
            $stmt_delete_ocupaciones = $conexion->prepare($sql_delete_ocupaciones);
            $stmt_delete_ocupaciones->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_delete_ocupaciones->execute();

            // Eliminar las reservas de recursos asociadas a todas las mesas de la sala
            $sql_delete_reservas = "DELETE FROM tbl_reservas WHERE id_mesa IN (SELECT id_mesa FROM tbl_mesas WHERE id_sala = :id_sala)";
            $stmt_delete_reservas = $conexion->prepare($sql_delete_reservas);
            $stmt_delete_reservas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_delete_reservas->execute();

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
        header("Location: ../menu-recursos.php?mensaje=recurso_eliminado");
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
