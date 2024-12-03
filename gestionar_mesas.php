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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body data-usuario="<?php echo htmlspecialchars($_SESSION['Usuario'], ENT_QUOTES, 'UTF-8'); ?>" data-sweetalert="<?php echo $_SESSION['sweetalert_mostrado'] ? 'true' : 'false'; ?>" data-mesa-sweetalert="<?php echo isset($_SESSION['mesa_sweetalert']) && $_SESSION['mesa_sweetalert'] ? 'true' : 'false'; ?>">
    <div class="container">
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
            
                // Verificación de parámetros GET
                if (isset($_GET['categoria']) && isset($_GET['id_sala'])) {
                    $categoria_seleccionada = $_GET['categoria'];
                    $id_sala = $_GET['id_sala'];
            
                    // Consultar las salas de acuerdo a la categoría seleccionada
                    $query_salas = "SELECT * FROM tbl_salas WHERE tipo_sala = :categoria_seleccionada AND id_sala = :id_sala";
                    $stmt_salas = $conexion->prepare($query_salas);
                    $stmt_salas->bindParam(':categoria_seleccionada', $categoria_seleccionada, PDO::PARAM_STR);
                    $stmt_salas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
                    $stmt_salas->execute();
            
                    if ($stmt_salas->rowCount() > 0) {
                        // Si la sala existe, obtener las mesas de esa sala
                        $query_mesas = "SELECT * FROM tbl_mesas WHERE id_sala = :id_sala";
                        $stmt_mesas = $conexion->prepare($query_mesas);
                        $stmt_mesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
                        $stmt_mesas->execute();
                        $mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);
            
                        if (count($mesas) > 0) {
                            foreach ($mesas as $mesa) {
                                $estado_actual = htmlspecialchars($mesa['estado']);
                                $estado_opuesto = $estado_actual === 'libre' ? 'Ocupar' : 'Liberar';
            
                                // Verificar si la mesa está ocupada y quién la ocupa
                                $mesa_id = $mesa['id_mesa'];
                                $query_ocupacion = "SELECT id_usuario FROM tbl_ocupaciones WHERE id_mesa = :mesa_id AND fecha_fin IS NULL";
                                $stmt_ocupacion = $conexion->prepare($query_ocupacion);
                                $stmt_ocupacion->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
                                $stmt_ocupacion->execute();
                                $id_usuario_ocupante = $stmt_ocupacion->fetchColumn();
                                $stmt_ocupacion->closeCursor();
            
                                // Si la mesa está ocupada por el usuario actual, mostrar el botón de liberación
                                $desactivar_boton = ($estado_actual === 'ocupada' && $id_usuario !== $id_usuario_ocupante);
                                echo "
                                <div class='mesa-card'>
                                    <h3>Mesa: " . htmlspecialchars($mesa['numero_mesa']) . "</h3>
                                    <div class='mesa-image'>
                                        <img src='./img/mesas/Mesa_" . htmlspecialchars($mesa['numero_sillas']) . ".png' alt='Mesa con " . htmlspecialchars($mesa['numero_sillas']) . " sillas'>
                                    </div>
                                    <div class='mesa-info'>
                                        <p><strong>Sala:</strong> " . htmlspecialchars($categoria_seleccionada) . "</p>
                                        <p><strong>Estado:</strong> 
                                            <span class='" . ($estado_actual === 'libre' ? 'estado-libre' : 'estado-ocupada') . "'>
                                                " . ucfirst($estado_actual) . "
                                            </span>
                                        </p>
                                        <p><strong>Sillas:</strong> " . htmlspecialchars($mesa['numero_sillas']) . "</p>
                                        <form method='POST'>
                                            <input type='hidden' name='mesa_id' value='" . htmlspecialchars($mesa['id_mesa']) . "'>
                                            <button type='submit' name='btn-editar-sillas' class='btn-editar-sillas'>
                                                Editar
                                            </button>
                                        </form>
                                    </div>
                                    <form method='POST' action='gestionar_mesas.php?categoria=" . htmlspecialchars($categoria_seleccionada) . "&id_sala=" . htmlspecialchars($id_sala) . "'>
                                        <input type='hidden' name='mesa_id' value='" . htmlspecialchars($mesa['id_mesa']) . "'>
                                        <input type='hidden' name='estado' value='" . htmlspecialchars($estado_actual) . "'>
                                        <button type='submit' name='cambiar_estado' class='btn-estado " . ($estado_actual === 'libre' ? 'btn-libre' : 'btn-ocupada') . "' 
                                            " . ($desactivar_boton ? 'disabled' : '') . ">
                                            " . ($estado_opuesto === 'Liberar' && $desactivar_boton ? 'No puedes liberar esta mesa' : htmlspecialchars($estado_opuesto)) . "
                                        </button>
                                    </form>";
                                
                                // Mostrar formulario de edición si el botón fue presionado
                                if (isset($_POST['btn-editar-sillas']) && $_POST['mesa_id'] == $mesa['id_mesa']) {
                                    echo "
                                    <div class='editar-sillas'>
                                        <form method='POST' action='editar_sillas.php?categoria=" . htmlspecialchars($categoria_seleccionada) . "&id_sala=" . htmlspecialchars($id_sala) . "'>
                                            <input type='hidden' name='mesa_id' value='" . htmlspecialchars($mesa['id_mesa']) . "'>
                                            <label for='numero_sillas_" . htmlspecialchars($mesa['id_mesa']) . "'>Número de sillas:</label>
                                            <input type='number' id='numero_sillas_" . htmlspecialchars($mesa['id_mesa']) . "' name='numero_sillas' value='" . htmlspecialchars($mesa['numero_sillas']) . "' min='1'>
                                            <button type='submit' name='editar_sillas'>Guardar cambios</button>
                                        </form>
                                    </div>";
                                }
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No hay mesas registradas en esta sala.</p>";
                        }
            
                        $stmt_mesas->closeCursor();
                    } else {
                        echo "<p>No se encontró la sala seleccionada o no corresponde a la categoría.</p>";
                    }
                    $stmt_salas->closeCursor();
                } else {
                    echo "<p>Faltan parámetros para la selección de sala o categoría.</p>";
                }
            
                // Manejar el cambio de estado de las mesas
                if (isset($_POST['cambiar_estado'])) {
                    $mesa_id = $_POST['mesa_id'];
                    $estado_nuevo = $_POST['estado'] == 'libre' ? 'ocupada' : 'libre';
                    $fecha_hora = date("Y-m-d H:i:s");
            
                    // Actualizar estado de la mesa
                    $query_update = "UPDATE tbl_mesas SET estado = :estado_nuevo WHERE id_mesa = :mesa_id";
                    $stmt_update = $conexion->prepare($query_update);
                    $stmt_update->bindParam(':estado_nuevo', $estado_nuevo, PDO::PARAM_STR);
                    $stmt_update->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
                    $stmt_update->execute();
            
                    // Si la mesa se ocupa, insertar la ocupación
                    if ($estado_nuevo == 'ocupada') {
                        $query_insert = "INSERT INTO tbl_ocupaciones (id_usuario, id_mesa, fecha_inicio) VALUES (:id_usuario, :mesa_id, :fecha_hora)";
                        $stmt_insert = $conexion->prepare($query_insert);
                        $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                        $stmt_insert->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
                        $stmt_insert->bindParam(':fecha_hora', $fecha_hora, PDO::PARAM_STR);
                        $stmt_insert->execute();
                    } else {
                        // Si la mesa se libera, actualizar la fecha de fin
                        $query_end = "UPDATE tbl_ocupaciones SET fecha_fin = :fecha_hora WHERE id_mesa = :mesa_id AND fecha_fin IS NULL";
                        $stmt_end = $conexion->prepare($query_end);
                        $stmt_end->bindParam(':fecha_hora', $fecha_hora, PDO::PARAM_STR);
                        $stmt_end->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
                        $stmt_end->execute();
                    }
            
                    // Establecer una variable de sesión para indicar que se debe mostrar el SweetAlert
                    $_SESSION['mesa_sweetalert'] = true;
                }
            
                // Confirmar la transacción
                $conexion->commit();
            
                // Redirigir después de cambiar el estado
                if (isset($_POST['cambiar_estado'])) {
                    header("Location: gestionar_mesas.php?categoria=$categoria_seleccionada&id_sala=$id_sala");
                    exit();
                }
            
                $conexion = null; // Cerrar la conexión PDO
            
            } catch (PDOException $e) {
                // Revertir la transacción en caso de error
                $conexion->rollBack();
                echo "Ocurrió un error al procesar la solicitud: " . $e->getMessage();
            }
            ?>
            
        </div>
        <script src="./js/sweetalert.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>