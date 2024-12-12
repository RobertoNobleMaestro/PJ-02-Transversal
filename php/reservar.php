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

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/formulario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        // Actualizar el formulario dinámico basado en el turno seleccionado
        function updateForm() {
            const turno = document.getElementById('turno').value;
            const formReserva = document.getElementById('form_reserva');

            if (turno !== "") {
                formReserva.classList.remove('hidden');
                loadTurnos(turno); // Cargar horarios según el turno seleccionado
            } else {
                formReserva.classList.add('hidden');
            }
        }

        // Cargar horarios dinámicamente según turno
        function loadTurnos(turno) {
            const horaInicio = document.getElementById('fecha_inicio');
            horaInicio.innerHTML = "<option value=''>Selecciona una hora</option>"; // Limpiar opciones previas

            let horas = turno === '1' ? ['12:00', '13:00', '14:00', '15:00'] : ['19:00', '20:00', '21:00', '22:00'];

            horas.forEach(hora => {
                const option = document.createElement('option');
                option.value = hora;
                option.textContent = hora;
                horaInicio.appendChild(option);
            });
        }

        // Actualizar automáticamente la hora final
        function updateHoraFin() {
            const horaInicio = document.getElementById('fecha_inicio').value;
            const horaFinal = document.getElementById('fecha_fin');

            if (horaInicio) {
                const [hora] = horaInicio.split(':');
                const nuevaHora = parseInt(hora) + 1; // Incrementar 1 hora
                horaFinal.value = `${nuevaHora}:00`;
            } else {
                horaFinal.value = ''; // Limpiar si no hay hora de inicio seleccionada
            }
        }
    </script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container-form">
        <h1>Gestión de Reservas</h1>
        <br>
        <h3 class="text-center">Sala: <?php echo htmlspecialchars($nombre_sala); ?> | Mesa: <?php echo htmlspecialchars($numero_mesa); ?></h3>
        <form action="./reserva-crear.php" method="post" class="mt-3">
            <label for="turno">Seleccionar Turno:</label>
            <select id="turno" name="id_turno" onchange="updateForm()" class="form-label">
                <option value="" selected disabled>Elige un turno</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo htmlspecialchars($turno['id_turno']); ?>">
                        <?php echo htmlspecialchars($turno['nombre_turno']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <!-- Formulario de Reserva -->
            <div id="form_reserva" class="hidden mt-3">
                <h3>Reservar</h3>

                <label for="fecha_inicio">Hora de la Reserva</label>
                <select id="fecha_inicio" name="fecha_inicio" onchange="updateHoraFin();" class="form-label">
                    <option value="">Selecciona una hora</option>
                </select><br>

                <label for="fecha_fin">Hora final de la Reserva</label>
                <input type="text" id="fecha_fin" name="fecha_fin" readonly class="form-label"><br>

                <label for="fecha_reserva">Día de la reserva</label>
                <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-label"><br>
                <br>
                <input type="hidden" name="id_sala" value="<?php echo $id_sala; ?>" class="form-label">
                <input type="hidden" name="mesa_id" value="<?php echo $mesa_id; ?>" class="form-label">
                <button type="submit" name="btn_reservar" id="reservar" class="form-button">Reservar</button>
                <br><br>
            </div>
            <br>
            <a href="../gestionar_mesas.php?id_sala=<?php echo urlencode($id_sala); ?>" class="cancelar-btn">Cancelar</a>
            </form>
    </div>
</body>

</html>
