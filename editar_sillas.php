<?php
require_once('./php/conexion.php');
session_start();

if (isset($_POST['editar_sillas']) && isset($_POST['mesa_id']) && isset($_POST['numero_sillas'])) {
    $mesa_id = isset($_POST['mesa_id']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_POST['mesa_id'])) : '';
    $numero_sillas = isset($_POST['numero_sillas']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_POST['numero_sillas'])) : '';

    if (!is_numeric($numero_sillas) || $numero_sillas <= 0) {
        header("Location: ../menu.php?error=numero_sillas_invalido");
        exit();
    }

    try {
        mysqli_autocommit($conexion, false);
        mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);

        $sql = "UPDATE tbl_mesas SET numero_sillas = ? WHERE id_mesa = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $numero_sillas, $mesa_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($conexion);

            $categoria = isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : '';
            $id_sala = isset($_GET['id_sala']) ? intval($_GET['id_sala']) : 0;

            header("Location: ./gestionar_mesas.php?categoria=" . $categoria . "&id_sala=" . $id_sala . "&success=sillas_actualizadas");
        } 
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        header("Location: ../menu.php?error=error_actualizando_sillas");
    }
} else {
    header("Location: ../menu.php?error=campos_vacios");
}
?>