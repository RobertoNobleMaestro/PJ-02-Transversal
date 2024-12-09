<?php
require_once('../php/conexion.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar si el ID de la mesa fue proporcionado
if (!isset($_GET['id_mesa']) || empty($_GET['id_mesa'])) {
    echo "ID de la mesa no especificado.";
    exit();
}

$id_mesa = $_GET['id_mesa'];

// Obtener los datos de la mesa y la sala asociada
try {
    $sql_get_mesa = "
        SELECT m.numero_mesa, m.numero_sillas, m.estado, s.id_sala, s.nombre_sala
        FROM tbl_mesas m
        JOIN tbl_salas s ON m.id_sala = s.id_sala
        WHERE m.id_mesa = :id_mesa
    ";
    $stmt_get_mesa = $conexion->prepare($sql_get_mesa);
    $stmt_get_mesa->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt_get_mesa->execute();

    $mesa_info = $stmt_get_mesa->fetch(PDO::FETCH_ASSOC);

    if (!$mesa_info) {
        echo "No se encontró la mesa con el ID especificado.";
        exit();
    }

    $id_sala = $mesa_info['id_sala'];
    $nombre_sala = $mesa_info['nombre_sala'];

} catch (Exception $e) {
    echo "Error al obtener los datos de la mesa: " . htmlspecialchars($e->getMessage());
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container-form">
        <h2>Eliminar Mesa</h2>
        <p>Estás a punto de eliminar la mesa <strong><?php echo htmlspecialchars($mesa_info['numero_mesa']); ?></strong> de la sala <strong><?php echo htmlspecialchars($nombre_sala); ?></strong>.</p>
        <form method="POST" action="eliminar_sala.php">
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($id_mesa); ?>">
            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($id_sala); ?>">

            <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
        <br>
        <button type="submit" class="btn btn-primary" onclick="window.location.href='../menu-recursos.php'">Volver</button>
    </div>
</body>
</html>
