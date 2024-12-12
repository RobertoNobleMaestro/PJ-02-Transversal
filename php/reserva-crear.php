<?php
session_start();
require_once('../php/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "1") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario con validaciones
    $id_sala = isset($_POST['id_sala']) ? htmlspecialchars($_POST['id_sala']) : null;
    $mesa_id = isset($_POST['mesa_id']) ? htmlspecialchars($_POST['mesa_id']) : null;
    $fecha_reserva = isset($_POST['fecha_reserva']) ? htmlspecialchars($_POST['fecha_reserva']) : null;
    $fecha_inicio = isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio']) : null;
    $fecha_fin = isset($_POST['fecha_fin']) ? htmlspecialchars($_POST['fecha_fin']) : null;
    $id_turno = isset($_POST['id_turno']) ? htmlspecialchars($_POST['id_turno']) : null;
    $id_usuario = $_SESSION['id_usuario']; // ID del usuario desde la sesión

    try {
        // Verificar que la fecha y hora no sean en el pasado
        $currentDateTime = new DateTime();
        $reservaDateTime = new DateTime("$fecha_reserva $fecha_inicio");

        if ($reservaDateTime < $currentDateTime) {
            header("Location: ../gestionar_mesa.php?error=fecha_pasada&id_sala=" . urlencode($id_sala));
            exit();
        }

        // Verificar que la hora final esté dentro del id_turno permitido
        $horaFinTurno = $id_turno === "Mediodía" ? "16:00:00" : "23:59:59";

        if ($fecha_fin > $horaFinTurno) {
            header("Location: reservar.php?error=hora_fuera_turno");
            exit();
        }

        // Verificar que la mesa pertenece a la sala
        $query = "SELECT * FROM tbl_mesas WHERE id_mesa = :mesa_id AND id_sala = :id_sala";
        $stmt = $conexion->prepare($query);
        $stmt->execute([
            ':mesa_id' => $mesa_id,
            ':id_sala' => $id_sala
        ]);

        if ($stmt->rowCount() === 0) {
            header("Location: reservar.php?error=mesa_invalida");
            exit();
        }

        // Verificar solapamiento de horarios en la mesa
        $query = "SELECT * FROM tbl_reservas 
                  WHERE id_mesa = :mesa_id 
                  AND fecha_reserva = :fecha_reserva
                  AND (
                      (:fecha_inicio BETWEEN fecha_inicio AND fecha_fin)
                      OR (:fecha_fin BETWEEN fecha_inicio AND fecha_fin)
                      OR (fecha_inicio BETWEEN :fecha_inicio AND :fecha_fin)
                  )";
        $stmt = $conexion->prepare($query);
        $stmt->execute([
            ':mesa_id' => $mesa_id,
            ':fecha_reserva' => $fecha_reserva,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
        ]);

        if ($stmt->rowCount() > 0) {
            header("Location: reservar.php?error=solapamiento");
            exit();
        }

        // Insertar la reserva en la tabla
        $insert = "INSERT INTO tbl_reservas (id_mesa, id_usuario, fecha_reserva, fecha_inicio, fecha_fin, id_turno)
                   VALUES (:mesa_id, :id_usuario, :fecha_reserva, :fecha_inicio, :fecha_fin, :id_turno)";
        $stmtInsert = $conexion->prepare($insert);
        $stmtInsert->execute([
            ':mesa_id' => $mesa_id,
            ':id_usuario' => $id_usuario,
            ':fecha_reserva' => $fecha_reserva,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':id_turno' => $id_turno
        ]);

        header("Location: ../registro.php");
    } catch (Exception $e) {
        echo "Error al procesar la reserva: " . htmlspecialchars($e->getMessage());
    }
}
?>
