<?php
session_start();
require_once('../php/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "1") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

$id_reserva = htmlspecialchars($_GET['id_reserva'] ?? '');
$id_mesa = $fecha_reserva = $fecha_inicio = $fecha_fin = $id_turno = "";
$numero_mesa = $nombre_sala = $turno_nombre = "";

try {
    // Obtener datos de la reserva
    $query_reserva = "
        SELECT r.id_reserva, r.fecha_reserva, r.fecha_inicio, r.fecha_fin, r.id_mesa, r.id_turno,
               m.numero_mesa, s.id_sala, t.nombre_turno
        FROM tbl_reservas r
        JOIN tbl_mesas m ON r.id_mesa = m.id_mesa
        JOIN tbl_salas s ON m.id_sala = s.id_sala
        JOIN tbl_turnos t ON r.id_turno = t.id_turno
        WHERE r.id_reserva = :id_reserva";
    $stmt_reserva = $conexion->prepare($query_reserva);
    $stmt_reserva->execute([':id_reserva' => $id_reserva]);
    $reserva = $stmt_reserva->fetch(PDO::FETCH_ASSOC);
      if ($reserva) {
        $fecha_reserva = htmlspecialchars($reserva['fecha_reserva']);
        $fecha_inicio = htmlspecialchars($reserva['fecha_inicio']);
        $fecha_fin = htmlspecialchars($reserva['fecha_fin']);
        $id_mesa = htmlspecialchars($reserva['id_mesa']);
        $id_turno = htmlspecialchars($reserva['id_turno']);
        $numero_mesa = htmlspecialchars($reserva['numero_mesa']);
        $id_sala = htmlspecialchars($reserva['id_sala']);
        $turno_nombre = htmlspecialchars($reserva['nombre_turno']);
    } else {
        throw new Exception('Reserva no encontrada.');
    }

} catch (Exception $e) {
    echo "Error al obtener la reserva: " . htmlspecialchars($e->getMessage());
    die();
}

// Obtener los turnos disponibles
try {
    $query_turnos = "SELECT id_turno, nombre_turno FROM tbl_turnos";
    $stmt_turnos = $conexion->prepare($query_turnos);
    $stmt_turnos->execute();
    $turnos = $stmt_turnos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al cargar los turnos: " . htmlspecialchars($e->getMessage());
    die();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/formulario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Reserva</title>
</head>
<body>
    <div class="container-form">
        <br>
        <h3 class="text-center">Sala: <?php echo htmlspecialchars($nombre_sala); ?> | Mesa: <?php echo htmlspecialchars($numero_mesa); ?></h3>
        <form id="editReservaForm" action="./reserva-editar.php" method="post" class="mt-3">
            <input type="hidden" name="id_reserva" value="<?php echo htmlspecialchars($id_reserva); ?>">

            <!-- Turno -->
            <label for="turno">Seleccionar Turno:</label>
            <select id="turno" name="id_turno" class="form-label">
                <option value="" disabled>Elige un turno</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo htmlspecialchars($turno['id_turno']); ?>" 
                            <?php echo ($turno['id_turno'] == $id_turno) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($turno['nombre_turno']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="turnoError" class="error"></span><br>

            <!-- Fecha de la Reserva -->
            <label for="fecha_reserva">Día de la Reserva:</label>
            <input type="date" id="fecha_reserva" name="fecha_reserva" value="<?php echo htmlspecialchars($fecha_reserva); ?>" class="form-label">
            <span id="fechaReservaError" class="error"></span><br>

            <!-- Hora de Inicio -->
            <label for="fecha_inicio">Hora de Inicio:</label>
            <select id="fecha_inicio" name="fecha_inicio" class="form-label">
                <!-- Las horas se cargarán dinámicamente con JS -->
            </select>
            <span id="fechaInicioError" class="error"></span><br>

            <!-- Hora de Fin -->
            <label for="fecha_fin">Hora de Fin:</label>
            <input type="time" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" class="form-label" readonly>
            <span id="fechaFinError" class="error"></span><br>

            <!-- Campos Ocultos -->
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($id_mesa); ?>">
            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($id_sala); ?>">
            <br>
            <button type="submit" id="editarReserva" class="form-button">Actualizar Reserva</button>
        </form>

        <br>
        <div class="text-mid">
            <a href="../registro.php" class="cancelar-btn">Cancelar</a>
        </div>       
    </div>

    <!-- Enlazamos con el archivo JS de validaciones -->
    <script src="../js/validaciones-editar-reserva.js" defer></script>
</body>
</html>
