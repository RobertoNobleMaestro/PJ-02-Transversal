<?php
session_start();
require_once('../php/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "1") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario con validaciones
$id_reserva = htmlspecialchars($_POST['id_reserva']) ;
$id_sala = htmlspecialchars($_POST['id_sala']) ;
$id_mesa = htmlspecialchars($_POST['id_mesa']) ;
$fecha_reserva = htmlspecialchars($_POST['fecha_reserva']) ;
$fecha_inicio = htmlspecialchars($_POST['fecha_inicio']) ;
$fecha_fin = htmlspecialchars($_POST['fecha_fin']) ;
$id_turno = htmlspecialchars($_POST['id_turno']) ;
$id_usuario = $_SESSION['id_usuario'];
    try {
        // Verificar que la fecha y hora no sean en el pasado
        $currentDateTime = new DateTime();
        $reservaDateTime = new DateTime("$fecha_reserva $fecha_inicio");

        if ($reservaDateTime < $currentDateTime) {
            header("Location: ../gestionar_mesas.php?id_sala=" . urlencode($id_sala) . "&error=fecha_pasada");
            exit();
        }

        // Verificar que la hora final esté dentro del id_turno permitido
        $horaFinTurno = $id_turno === "Mediodía" ? "16:00:00" : "23:59:59";

        if ($fecha_fin > $horaFinTurno) {
            header("Location: reserva-editar.php?error=hora_fuera_turno");
            exit();
        }

        // Verificar que la mesa pertenece a la sala
        $query = "SELECT * FROM tbl_mesas WHERE id_mesa = :id_mesa AND id_sala = :id_sala";
        $stmt = $conexion->prepare($query);
        $stmt->execute([ 
            ':id_mesa' => $id_mesa,
            ':id_sala' => $id_sala
        ]);
        
        // // Comprobar si la mesa existe en la sala
        // if ($stmt->rowCount() === 0) {
        //     // Si no se encuentra, redirigir con el error
        //     header("Location: reserva-editar.php?error=mesa_invalida");
        //     exit();
        // }
        

        // Verificar solapamiento de horarios en la mesa
        $query = "SELECT * FROM tbl_reservas 
                  WHERE id_mesa = :id_mesa 
                  AND fecha_reserva = :fecha_reserva
                  AND id_reserva != :id_reserva
                  AND (
                      (:fecha_inicio BETWEEN fecha_inicio AND fecha_fin)
                      OR (:fecha_fin BETWEEN fecha_inicio AND fecha_fin)
                      OR (fecha_inicio BETWEEN :fecha_inicio AND :fecha_fin)
                  )";
        $stmt = $conexion->prepare($query);
        $stmt->execute([ 
            ':id_mesa' => $id_mesa,
            ':fecha_reserva' => $fecha_reserva,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':id_reserva' => $id_reserva
        ]);

        if ($stmt->rowCount() > 0) {
            header("Location: reserva-editar.php?error=solapamiento");
            exit();
        }

        // Actualizar la reserva en la base de datos
        $update = "UPDATE tbl_reservas 
                   SET id_mesa = :id_mesa, 
                       id_usuario = :id_usuario, 
                       fecha_reserva = :fecha_reserva, 
                       fecha_inicio = :fecha_inicio, 
                       fecha_fin = :fecha_fin, 
                       id_turno = :id_turno
                   WHERE id_reserva = :id_reserva";
        $stmtUpdate = $conexion->prepare($update);
        $stmtUpdate->execute([
            ':id_mesa' => $id_mesa,
            ':id_usuario' => $id_usuario,
            ':fecha_reserva' => $fecha_reserva,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':id_turno' => $id_turno,
            ':id_reserva' => $id_reserva
        ]);

        header("Location: ../gestionar_mesas.php?id_sala=" . urlencode($id_sala) . "&success=reserva_editada");
    } catch (Exception $e) {
        echo "Error al procesar la reserva: " . htmlspecialchars($e->getMessage());
    }
}
?>
