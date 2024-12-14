<?php
require_once('./php/conexion.php');
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}


try {
    // Obtener los filtros de los parámetros GET
    $usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : '';
    $sala_filter = isset($_GET['sala']) ? $_GET['sala'] : '';
    $tipo_sala_filter = isset($_GET['tipo_sala']) ? $_GET['tipo_sala'] : '';

    // Construir la consulta con los filtros aplicados
    $sql = "
        SELECT 
            s.id_sala,
            s.nombre_sala,
            s.tipo_sala,
            m.id_mesa,
            m.numero_mesa,
            s.imagen_sala,
            m.numero_sillas
        FROM 
            tbl_salas s
        LEFT JOIN 
            tbl_mesas m ON s.id_sala = m.id_sala
        WHERE 1=1";  // Agregar una cláusula WHERE inicial para facilitar la concatenación de filtros

    // Aplicar filtros
    if ($usuario_filter) {
        $sql .= " AND m.id_usuario = :usuario";
    }
    if ($sala_filter) {
        $sql .= " AND s.id_sala = :sala";
    }
    if ($tipo_sala_filter) {
        $sql .= " AND s.tipo_sala = :tipo_sala";
    }

    // Ordenar los resultados
    $sql .= " ORDER BY s.tipo_sala, s.nombre_sala, m.numero_mesa";

    // Ejecutar la consulta
    $stmt = $conexion->prepare($sql);

    // Vincular los parámetros de los filtros si se seleccionaron
    if ($usuario_filter) {
        $stmt->bindParam(':usuario', $usuario_filter, PDO::PARAM_INT);
    }
    if ($sala_filter) {
        $stmt->bindParam(':sala', $sala_filter, PDO::PARAM_INT);
    }
    if ($tipo_sala_filter) {
        $stmt->bindParam(':tipo_sala', $tipo_sala_filter, PDO::PARAM_STR);
    }

    $stmt->execute();

    // Agrupar los resultados por tipo de sala y nombre de sala
    $salas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $salas[$row['tipo_sala']][$row['nombre_sala']][] = $row;
    }

    // Obtener tipos de sala únicos para el filtro
    $query_tipos = "SELECT DISTINCT tipo_sala FROM tbl_salas";
    $stmt_tipos = $conexion->query($query_tipos);
    $tipos_sala = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los datos: " . htmlspecialchars($e->getMessage());
    die();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu-recursos.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./menu-admin.php"><img src="./img/user-icon.svg" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            <div class="navbar-title">
                <h3>Lista de recursos</h3>
            </div>
            <div class="navbar-right">
                <a href="./php/salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
        <br>

        <!-- Formulario de Filtros -->
        <form method="GET" action="menu-recursos.php" class="mt-3">
            <div class="d-flex flex-wrap align-items-center">
                <!-- Filtro por Sala -->
                <div class="me-3 mb-3">
                    <label for="sala" class="text-white">Sala:</label>
                    <select name="sala" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="">Todas</option>
                        <?php
                        $query_salas = "SELECT id_sala, nombre_sala FROM tbl_salas";
                        $stmt_salas = $conexion->query($query_salas);
                        while ($sala = $stmt_salas->fetch(PDO::FETCH_ASSOC)) {
                            $selected = isset($_GET['sala']) && $_GET['sala'] == $sala['id_sala'] ? 'selected' : '';
                            echo "<option value='{$sala['id_sala']}' $selected>{$sala['nombre_sala']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Filtro por Tipo de Sala -->
                <div class="me-3 mb-3">
                    <label for="tipo_sala" class="text-white">Tipo de Sala:</label>
                    <select name="tipo_sala" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="">Todos</option>
                        <?php
                        foreach ($tipos_sala as $tipo) {
                            $selected = isset($_GET['tipo_sala']) && $_GET['tipo_sala'] == $tipo['tipo_sala'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($tipo['tipo_sala']) . "' $selected>" . htmlspecialchars($tipo['tipo_sala']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Botones -->
                <div class="me-3 mb-3">
                    <button type="submit" class="btn btn-primary btn-sm" style="height: 40px; width: 200px; margin-top: 25px;">Filtrar</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='menu-recursos.php'" style="height: 40px; width: 200px; margin-left: 7px; margin-top: 25px;">Borrar Filtros</button>
                </div>
            </div>
        </form>

        <br>
        <button class="btn btn-primary" onclick="location.href='./crud-recursos/añadir_recurso.php'">Añadir recurso</button>

        <br>
        <?php
        foreach ($salas as $tipo_sala => $salas_tipo) {
            echo "<div class='tabla-container'>";
            echo "<h2 class='titulos'>" . htmlspecialchars($tipo_sala) . "</h2>";

            echo "<table class='tabla'>";
            echo "<thead><tr><th>Nombre de la Sala</th><th>Imagen de la sala</th><th>Número de Mesa</th><th>Número de Sillas</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";

            foreach ($salas_tipo as $nombre_sala => $mesas_sala) {
                foreach ($mesas_sala as $index => $mesa) {
                    if ($index === 0) {
                        echo "<tr><td rowspan='" . count($mesas_sala) . "'>" . htmlspecialchars($nombre_sala) . "</td>";
                        echo "<td rowspan='" . count($mesas_sala) . "'><img src='./img/" . htmlspecialchars($mesa['imagen_sala']) . "' alt='Imagen de la sala' style='width: 150px; object-fit: cover;'></td>";
                    }

                    echo "<td>" . htmlspecialchars($mesa['numero_mesa']) . "</td>";
                    echo "<td>" . htmlspecialchars($mesa['numero_sillas']) . "</td>";
                    echo "<td class='btn-container'>";
                    echo "<a href='./crud-recursos/editar_recurso.php?id_mesa=" . $mesa['id_mesa'] . "' class='btn btn-warning'>Editar</a>";
                    echo "<a href='./crud-recursos/eliminar_recurso.php?id_mesa=" . $mesa['id_mesa'] . "' class='btn btn-danger'>Eliminar</a>";
                    echo "</td></tr>";
                }
            }
            echo "</tbody></table></div>";
        }
        ?>
    </div>
    <script src="./js/sweat_alert_recursos.js"></script>
</body>
</html>
   