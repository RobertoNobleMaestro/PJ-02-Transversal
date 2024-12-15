<?php
session_start();
require_once('./php/conexion.php');

// Verificar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Historial de reservas</title>
</head>

<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <div class="navbar-title">
                <h3>Historial de reservas</h3>
            </div>

            <div class="navbar-right" style="margin-right: 18px;">
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="./php/salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <br>

    <div id="historial-container" class="container">
        <h2 id="titulo-historial" class="text-white" style="background-color: none;">Historial de reservas</h2>

        <form method="GET" action="registro.php" class="mt-3">
            <div class="d-flex flex-wrap align-items-center">
                <!-- Filtros (Desplegables) -->
                <div class="d-flex flex-wrap align-items-center me-3 mb-3">
                    <div class="me-3">
                        <label for="usuario" class="text-white">Usuario:</label>
                        <select name="usuario" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                            <option value="">Todos</option>
                            <?php
                            // Consulta para usuarios
                            $query_usuarios = "SELECT id_usuario, nombre_user FROM tbl_usuarios";
                            $stmt_usuarios = $conexion->query($query_usuarios);
                            while ($usuario = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)) {
                                $selected = isset($_GET['usuario']) && $_GET['usuario'] == $usuario['id_usuario'] ? 'selected' : '';
                                echo "<option value='{$usuario['id_usuario']}' $selected>{$usuario['nombre_user']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="me-3">
                        <label for="sala" class="text-white">Sala:</label>
                        <select name="sala" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                            <option value="">Todas</option>
                            <?php
                            // Consulta para salas
                            $query_salas = "SELECT id_sala, nombre_sala FROM tbl_salas";
                            $stmt_salas = $conexion->query($query_salas);
                            while ($sala = $stmt_salas->fetch(PDO::FETCH_ASSOC)) {
                                $selected = isset($_GET['sala']) && $_GET['sala'] == $sala['id_sala'] ? 'selected' : '';
                                echo "<option value='{$sala['id_sala']}' $selected>{$sala['nombre_sala']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="me-3">
                        <label for="mesa" class="text-white">Mesa:</label>
                        <select name="mesa" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                            <option value="">Todas</option>
                            <?php
                            // Consulta para mesas
                            $query_mesas = "SELECT id_mesa, numero_mesa FROM tbl_mesas";
                            $stmt_mesas = $conexion->query($query_mesas);
                            while ($mesa = $stmt_mesas->fetch(PDO::FETCH_ASSOC)) {
                                $selected = isset($_GET['mesa']) && $_GET['mesa'] == $mesa['id_mesa'] ? 'selected' : '';
                                echo "<option value='{$mesa['id_mesa']}' $selected>{$mesa['numero_mesa']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="me-3">
                        <label for="turno" class="text-white">Turno:</label>
                        <select name="turno" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                            <option value="">Todos</option>
                            <?php
                            // Consulta para turnos
                            $query_turnos = "SELECT id_turno, nombre_turno FROM tbl_turnos";
                            $stmt_turnos = $conexion->query($query_turnos);
                            while ($turno = $stmt_turnos->fetch(PDO::FETCH_ASSOC)) {
                                $selected = isset($_GET['turno']) && $_GET['turno'] == $turno['id_turno'] ? 'selected' : '';
                                echo "<option value='{$turno['id_turno']}' $selected>{$turno['nombre_turno']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary btn-sm me-2" style="height: 40px; width: 200px; margin-top: 9px;">Filtrar</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='registro.php'" style="height: 40px; width: 200px; margin-left: 7px; margin-top: 9px;">Borrar Filtros</button>
                    </div>
                </div>
            </div>
        </form>

        <?php
        // Variables para los filtros
        $usuario_filter = isset($_GET['usuario']) && !empty($_GET['usuario']) ? $_GET['usuario'] : '';
        $sala_filter = isset($_GET['sala']) && !empty($_GET['sala']) ? $_GET['sala'] : '';
        $mesa_filter = isset($_GET['mesa']) && !empty($_GET['mesa']) ? $_GET['mesa'] : '';
        $turno_filter = isset($_GET['turno']) && !empty($_GET['turno']) ? $_GET['turno'] : '';

        // Consulta SQL con filtros
        $query_historial = "SELECT o.id_reserva, u.nombre_user, s.nombre_sala, m.numero_mesa, t.nombre_turno, o.fecha_inicio, o.fecha_fin, o.fecha_reserva
                            FROM tbl_reservas o
                            JOIN tbl_mesas m ON o.id_mesa = m.id_mesa
                            JOIN tbl_salas s ON m.id_sala = s.id_sala
                            JOIN tbl_usuarios u ON o.id_usuario = u.id_usuario
                            JOIN tbl_turnos t ON o.id_turno = t.id_turno";

        $filters = [];
        if ($usuario_filter) {
            $filters[] = "u.id_usuario = :usuario";
        }
        if ($sala_filter) {
            $filters[] = "s.id_sala = :sala";
        }
        if ($mesa_filter) {
            $filters[] = "m.id_mesa = :mesa";
        }
        if ($turno_filter) {
            $filters[] = "t.id_turno = :turno";
        }
        if (!empty($filters)) {
            $query_historial .= " WHERE " . implode(" AND ", $filters);
        }

        // Preparar la consulta
        $stmt_historial = $conexion->prepare($query_historial);

        // Vincular parámetros
        if ($usuario_filter) {
            $stmt_historial->bindParam(':usuario', $usuario_filter, PDO::PARAM_INT);
        }
        if ($sala_filter) {
            $stmt_historial->bindParam(':sala', $sala_filter, PDO::PARAM_INT);
        }
        if ($mesa_filter) {
            $stmt_historial->bindParam(':mesa', $mesa_filter, PDO::PARAM_INT);
        }
        if ($turno_filter) {
            $stmt_historial->bindParam(':turno', $turno_filter, PDO::PARAM_INT);
        }

        // Ejecutar la consulta
        $stmt_historial->execute();
        ?>

        <!-- Mostrar resultados en tabla -->
        <div class="table-responsive mt-4">
            <table class="table table-striped" style="text-align:center;">
                <thead class="thead-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Sala</th>
                        <th>Número de Mesa</th>
                        <th>Turno</th>
                        <th>Fecha de la reserva</th>
                        <th>Hora inicio de la reserva</th>
                        <th>Hora fin de la reserva</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($ocupacion = $stmt_historial->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$ocupacion['nombre_user']}</td>
                            <td>{$ocupacion['nombre_sala']}</td>
                            <td>{$ocupacion['numero_mesa']}</td>
                            <td>{$ocupacion['nombre_turno']}</td>
                            <td>{$ocupacion['fecha_reserva']}</td>
                            <td>{$ocupacion['fecha_inicio']}</td>
                            <td>{$ocupacion['fecha_fin']}</td>
                            <td>
                                <a href='./php/editar_reserva.php?id_reserva={$ocupacion['id_reserva']}' class='btn btn-warning btn-sm'>Editar</a>
                                <a href='./php/cancelar_reserva.php?id_reserva={$ocupacion['id_reserva']}'  class='btn btn-danger btn-sm'>Cancelar</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="./js/sweet_alerts.js"></script>
</body>

</html>
