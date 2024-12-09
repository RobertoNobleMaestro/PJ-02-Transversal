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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Historial de Ocupaciones</title>
</head>

<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <div class="navbar-title">
                <h3>Historial de ocupaciones</h3>
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
        <h2 id="titulo-historial" class="text-white" style="background-color: none;">Historial de Ocupaciones</h2>

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
                        <label for="estado" class="text-white">Estado Sala:</label>
                        <select name="estado" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                            <option value="">Todos</option>
                            <option value="libre" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'libre') ? 'selected' : ''; ?>>Libre</option>
                            <option value="ocupada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'ocupada') ? 'selected' : ''; ?>>Ocupada</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary btn-sm me-2" style="height: 40px; width: 200px; margin-top: 10px;">Filtrar</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='registro.php'" style="height: 40px; width: 200px; margin-left: 7px;">Borrar Filtros</button>
                    </div>
                </div>
            </div>
        </form>

        <?php
        // Variables para los filtros
        $usuario_filter = isset($_GET['usuario']) && !empty($_GET['usuario']) ? $_GET['usuario'] : '';
        $sala_filter = isset($_GET['sala']) && !empty($_GET['sala']) ? $_GET['sala'] : '';
        $mesa_filter = isset($_GET['mesa']) && !empty($_GET['mesa']) ? $_GET['mesa'] : '';
        $estado_filter = isset($_GET['estado']) && !empty($_GET['estado']) ? $_GET['estado'] : '';
        ?>

        <?php
        // Consulta SQL con filtros
        $query_historial = "SELECT u.nombre_user, s.nombre_sala, m.numero_mesa, m.estado, 
                                       o.fecha_inicio, o.fecha_fin, 
                                       TIMESTAMPDIFF(MINUTE, o.fecha_inicio, o.fecha_fin) AS duracion
                            FROM tbl_ocupaciones o
                            JOIN tbl_mesas m ON o.id_mesa = m.id_mesa
                            JOIN tbl_salas s ON m.id_sala = s.id_sala
                            JOIN tbl_usuarios u ON o.id_usuario = u.id_usuario";

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
        if ($estado_filter) {
            $filters[] = "m.estado = :estado";
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
        if ($estado_filter) {
            $stmt_historial->bindParam(':estado', $estado_filter, PDO::PARAM_STR);
        }

        // Ejecutar la consulta
        $stmt_historial->execute();
        ?>

        <!-- Mostrar resultados en tabla -->
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Sala</th>
                        <th>Número de Mesa</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Duración (minutos)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($ocupacion = $stmt_historial->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                        <td>{$ocupacion['nombre_user']}</td>
                        <td>{$ocupacion['nombre_sala']}</td>
                        <td>{$ocupacion['numero_mesa']}</td>
                        <td>{$ocupacion['estado']}</td>
                        <td>{$ocupacion['fecha_inicio']}</td>
                        <td>{$ocupacion['fecha_fin']}</td>
                        <td>{$ocupacion['duracion']}</td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="./js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>