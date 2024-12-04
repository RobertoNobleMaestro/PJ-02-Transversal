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
    $tipo_sala = htmlspecialchars($_POST['tipo_sala']);
    $nombre_sala = htmlspecialchars($_POST['nombre_sala']);
    $numero_mesa = htmlspecialchars($_POST['numero_mesa']);
    $numero_sillas = htmlspecialchars($_POST['numero_sillas']);
    $estado = htmlspecialchars($_POST['estado']);
    $id_sala = htmlspecialchars($_POST['id_sala']);

    try {
        // Iniciar la transacción
        $conexion->beginTransaction();

        // Actualizar los datos de la mesa
        $sql_update_mesa = "
            UPDATE tbl_mesas 
            SET 
                numero_mesa = :numero_mesa,
                numero_sillas = :numero_sillas,
                estado = :estado
            WHERE id_mesa = :id_mesa
        ";
        $stmt_update_mesa = $conexion->prepare($sql_update_mesa);
        $stmt_update_mesa->bindParam(':numero_mesa', $numero_mesa, PDO::PARAM_INT);
        $stmt_update_mesa->bindParam(':numero_sillas', $numero_sillas, PDO::PARAM_INT);
        $stmt_update_mesa->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt_update_mesa->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_update_mesa->execute();

        // Obtener el nombre actual de la sala para comparar si se ha cambiado
        $sql_check_sala = "SELECT nombre_sala FROM tbl_salas WHERE id_sala = :id_sala";
        $stmt_check_sala = $conexion->prepare($sql_check_sala);
        $stmt_check_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmt_check_sala->execute();
        $current_sala = $stmt_check_sala->fetch(PDO::FETCH_ASSOC);

        // Si el nombre de la sala ha cambiado, actualizamos la tabla tbl_salas
        if ($current_sala['nombre_sala'] != $nombre_sala) {
            $sql_update_sala = "
                UPDATE tbl_salas 
                SET nombre_sala = :nombre_sala 
                WHERE id_sala = :id_sala
            ";
            $stmt_update_sala = $conexion->prepare($sql_update_sala);
            $stmt_update_sala->bindParam(':nombre_sala', $nombre_sala, PDO::PARAM_STR);
            $stmt_update_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_update_sala->execute();
        }

        // Si todo fue bien, hacemos commit de la transacción
        $conexion->commit();

        // Redirigir al menú con mensaje de éxito
        header("Location: ../menu-recursos.php?success=recurso_actualizado");
        exit();
    } catch (Exception $e) {
        // Si ocurre un error, deshacemos la transacción
        $conexion->rollBack();
        echo "Error al actualizar los datos: " . htmlspecialchars($e->getMessage());
        die();
    }
}
?>
