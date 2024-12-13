<?php
session_start();
require_once('../php/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "1") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

// Variables de sala y mesa
$id_sala = htmlspecialchars($_GET['id_sala'] ?? '');
$mesa_id = htmlspecialchars($_POST['mesa_id'] ?? '');

try {
    $query = "SELECT nombre_sala, numero_mesa FROM tbl_salas 
              INNER JOIN tbl_mesas ON tbl_salas.id_sala = tbl_mesas.id_sala 
              WHERE tbl_salas.id_sala = :id_sala AND tbl_mesas.id_mesa = :mesa_id";
    $stmt = $conexion->prepare($query);
    $stmt->execute([':id_sala' => $id_sala, ':mesa_id' => $mesa_id]);
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_sala = $sala['nombre_sala'] ?? 'Sala Desconocida';
    $numero_mesa = $sala['numero_mesa'] ?? 'Mesa Desconocida';
} catch (Exception $e) {
    $nombre_sala = 'Error al cargar el nombre de la sala';
    $numero_mesa = 'Error al cargar el número de la mesa';
}

// Obtener turnos desde la base de datos
try {
    $query = "SELECT id_turno, nombre_turno FROM tbl_turnos";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
    <title>Gestión de Reservas</title>
</head>

<body>
    <div class="container-form">
        <h1>Gestión de Reservas</h1>
        <br>
        <h3 class="text-center">Sala: <?php echo htmlspecialchars($nombre_sala); ?> | Mesa: <?php echo htmlspecialchars($numero_mesa); ?></h3>
        <form id="reservaForm" action="./reserva-crear.php" method="post" class="mt-3">
            <label for="turno">Seleccionar Turno:</label>
            <select id="turno" name="id_turno" class="form-label">
                <option value="" selected disabled>Elige un turno</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo htmlspecialchars($turno['id_turno']); ?>">
                        <?php echo htmlspecialchars($turno['nombre_turno']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="turnoError" class="error"></span><br>

            <!-- Formulario de Reserva -->
            <div id="form_reserva" class="hidden mt-3">
                <h3>Reservar</h3>

                <label for="fecha_inicio">Hora de la Reserva:</label>
                <select id="fecha_inicio" name="fecha_inicio" class="form-label">
                    <option value="" disabled selected>Selecciona una hora</option>
                </select>
                <span id="fechaInicioError" class="error"></span><br>

                <label for="fecha_fin">Hora final de la Reserva:</label>
                <input type="text" id="fecha_fin" name="fecha_fin" readonly class="form-label">
                <span id="fechaFinError" class="error"></span><br>

                <label for="fecha_reserva">Día de la Reserva:</label>
                <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-label">
                <span id="fechaReservaError" class="error"></span><br>
                <br>

                <input type="hidden" name="id_sala" value="<?php echo $id_sala; ?>">
                <input type="hidden" name="mesa_id" value="<?php echo $mesa_id; ?>">

                <button type="submit" id="reservar" class="form-button">Reservar</button>
            </div>
            <br>
            <div class="text-mid">
                <a href="../gestionar_mesas.php?id_sala=<?php echo urlencode($id_sala); ?>" class="cancelar-btn">Cancelar</a>
            </div>
        </form>
    </div>
    <script src="../js/validaciones-reservas.js" defer></script>
</body>

</html>
