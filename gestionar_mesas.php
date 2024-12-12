<?php
session_start();
date_default_timezone_set('Europe/Madrid');
require_once('./php/conexion.php');

// Verificación de sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

$id_sala = isset($_GET['id_sala']) ? $_GET['id_sala'] : 0;

try {
    if ($id_sala === 0) {
        throw new Exception("ID de sala no válido.");
    }

    // Consulta para obtener el nombre de la sala
    $query_nombre_sala = "SELECT nombre_sala FROM tbl_salas WHERE id_sala = :id_sala";
    $stmt_nombre_sala = $conexion->prepare($query_nombre_sala);

    // Vinculamos el parámetro :id_sala
    $stmt_nombre_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);

    // Ejecutamos la consulta
    $stmt_nombre_sala->execute();

    // Verificamos si hay resultados
    $nombre_sala = $stmt_nombre_sala->fetch(PDO::FETCH_ASSOC);
    if (!$nombre_sala) {
        throw new Exception("No se encontró ninguna sala con el ID especificado.");
    }

    $nombre_sala = $nombre_sala['nombre_sala'];

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
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
</head>

<body data-usuario="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" data-sweetalert="<?php echo $_SESSION['sweetalert_mostrado'] ? 'true' : 'false'; ?>" data-mesa-sweetalert="<?php echo isset($_SESSION['mesa_sweetalert']) && $_SESSION['mesa_sweetalert'] ? 'true' : 'false'; ?>" data-nombre-sala="<?php echo htmlspecialchars($nombre_sala); ?>">    <div class="container">
        <nav class="navegacion">
            <!-- Sección izquierda con el logo grande y el ícono adicional más pequeño -->
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <!-- Título en el centro -->
            <div class="navbar-title">
                <h3><?php echo htmlspecialchars($nombre_sala); ?></h3>
            </div>

            <div class="navbar-right" style="margin-right: 18px;">
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <!-- Icono de logout a la derecha -->
            <div class="navbar-right">
                <a href="./php/salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>

        <div class='mesas-container'>
            <?php
            // Inicia la salida de buffer para evitar errores de encabezados ya enviados
            ob_start();
            $id_sala = isset($_GET['id_sala']) ? $_GET['id_sala'] : 0;
            
            try {
                // Conexión PDO
                $conexion->beginTransaction(); // Iniciar la transacción
            
                // Obtener el id_usuario desde la sesión
                $usuario = $_SESSION['usuario'];
            
                // Obtener id_usuario de la base de datos
                $query_usuario = "SELECT id_usuario FROM tbl_usuarios WHERE nombre_user = :usuario";
                $stmt_usuario = $conexion->prepare($query_usuario);
                $stmt_usuario->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $stmt_usuario->execute();
                $id_usuario = $stmt_usuario->fetchColumn(); // Obtener el id_usuario
                $stmt_usuario->closeCursor();
            
                // Consultar las mesas de la sala
                $query_mesas = "SELECT * FROM tbl_mesas WHERE id_sala = :id_sala";
                $stmt_mesas = $conexion->prepare($query_mesas);
                $stmt_mesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
                $stmt_mesas->execute();
                $mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);
            
                if (count($mesas) > 0) {
                    foreach ($mesas as $mesa) {            
                        // Contar las reservas activas para cada mesa
                        $mesa_id = $mesa['id_mesa'];
                        $query_reservas_count = "SELECT COUNT(*) FROM tbl_reservas WHERE id_mesa = :mesa_id AND fecha_fin IS NULL";
                        $stmt_reservas_count = $conexion->prepare($query_reservas_count);
                        $stmt_reservas_count->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
                        $stmt_reservas_count->execute();
                        $reservas_count = $stmt_reservas_count->fetchColumn();
                        $stmt_reservas_count->closeCursor();

                        // Verificamos si la mesa está ocupada y mostramos el contador de reservas
                        echo "
                        <div class='mesa-card'>
                            <h3>Mesa: " . htmlspecialchars($mesa['numero_mesa']) . "</h3>
                            <div class='mesa-image'>
                                <img src='./img/mesas/Mesa_" . htmlspecialchars($mesa['numero_sillas']) . ".png' alt='Mesa con " . htmlspecialchars($mesa['numero_sillas']) . " sillas'>
                            </div>
                            <div class='mesa-info'>
                                <p><strong>Sala:</strong> " . htmlspecialchars($nombre_sala) . "</p>
                                <p><strong>Reservas activas:</strong> " . htmlspecialchars($reservas_count) . "</p>
                                <p><strong>Sillas:</strong> " . htmlspecialchars($mesa['numero_sillas']) . "</p>
                                <form method='POST' action='./php/reservar.php?id_sala=" . htmlspecialchars($id_sala) . "'>
                                    <input type='hidden' name='mesa_id' value='" . htmlspecialchars($mesa['id_mesa']) . "'>
                                    <button type='submit' name='btn-reservar' class='btn-estado'>
                                        Reservar
                                    </button>
                                </form>
                            </div>
                        </div>
                    ";
                    
                    }
                } else {
                    echo "<p>No hay mesas registradas en esta sala.</p>";
                }

                $stmt_mesas->closeCursor();
            } catch (PDOException $e) {
                // Manejo de errores de base de datos
                echo "Error de base de datos: " . $e->getMessage();
            }
            ?>
        </div>

        <script src="./js/sweetalert.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </div>
</body>

</html>
