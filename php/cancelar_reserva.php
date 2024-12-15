<?php
session_start();
require_once('../php/conexion.php');

// Verificar si el usuario tiene permisos para eliminar reservas
if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "1") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar que el ID de la reserva a eliminar esté presente y sea válido
if (isset($_GET['id_reserva'])) {
    $id_reserva = intval($_GET['id_reserva']);
    try {
        // Consultar la información de la reserva para mostrar en el mensaje
        $sql_reserva = "SELECT r.id_reserva, r.fecha_reserva, r.fecha_inicio, r.fecha_fin, m.numero_mesa, u.nombre_user, m.id_sala
                        FROM tbl_reservas r
                        JOIN tbl_mesas m ON r.id_mesa = m.id_mesa
                        JOIN tbl_usuarios u ON r.id_usuario = u.id_usuario
                        WHERE r.id_reserva = :id_reserva";
        $stmt_reserva = $conexion->prepare($sql_reserva);
        $stmt_reserva->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
        $stmt_reserva->execute();

        $reserva = $stmt_reserva->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            echo "<p>Reserva no encontrada.</p>";
            exit();
        }

        $id_sala = $reserva['id_sala'];  // Obtener id_sala

        // Si se confirma la eliminación
        if (isset($_POST['confirmar_eliminar'])) {
            try {
                $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conexion->beginTransaction();

                // Eliminar la reserva
                $sql_reserva_delete = "DELETE FROM tbl_reservas WHERE id_reserva = :id_reserva";
                $stmt_reserva_delete = $conexion->prepare($sql_reserva_delete);
                $stmt_reserva_delete->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
                if (!$stmt_reserva_delete->execute()) {
                    throw new Exception("Error al eliminar la reserva.");
                }

                // Confirmar la transacción
                $conexion->commit();

                // Redirigir con un mensaje de éxito y el id_sala
                header('Location: ../gestionar_mesas.php?mensaje=Reserva_eliminada_correctamente&id_sala=' . $id_sala);
                exit();
            } catch (Exception $e) {
                // Revertir la transacción en caso de error
                $conexion->rollBack();
                
                // Redirigir con un mensaje de error
                header("Location: ../registro.php?id=$id_reserva&error=" . urlencode($e->getMessage()));
                exit();
            }
        }
        
    } catch (Exception $e) {
        // En caso de error al buscar la reserva
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>ID de reserva no válido.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/formulario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container-form">
        <h1>Eliminar Reserva</h1>
        <p style="text-align: center;">¿Estás seguro de que deseas eliminar la reserva realizada por <strong><?php echo htmlspecialchars($reserva['nombre_user']); ?></strong> para la mesa número <strong><?php echo htmlspecialchars($reserva['numero_mesa']); ?></strong> el día <strong><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></strong> de <strong><?php echo htmlspecialchars($reserva['fecha_inicio']); ?></strong> a <strong><?php echo htmlspecialchars($reserva['fecha_fin']); ?></strong>?</p>
        <form action="cancelar_reserva.php?id_reserva=<?php echo $id_reserva; ?>" method="post">
            <button type="submit" name="confirmar_eliminar" class="form-button">Confirmar Eliminación</button>
            <br><br>
        </form>
        <div class="text-mid">
            <a href="../registro.php" class="cancelar-btn">Cancelar</a>
        </div>
    </div>
</body>
</html>
