<?php
require_once('../php/conexion.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

// Obtener los parámetros de la URL
$id_mesa = $_GET['id_mesa'];

// Inicializar variables para los datos de la mesa
$numero_mesa = $numero_sillas = $estado = "";
$tipo_sala = $nombre_sala = $imagen_sala = "";

// Consultar los datos de la mesa para mostrar en el formulario
try {
    $sql = "
        SELECT 
            m.numero_mesa,
            m.numero_sillas,
            s.id_sala,
            s.imagen_sala
        FROM 
            tbl_mesas m
        JOIN tbl_salas s ON m.id_sala = s.id_sala
        WHERE 
            m.id_mesa = :id_mesa
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();

    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    // Asignar valores recuperados a las variables
    if ($mesa) {
        $numero_mesa = $mesa['numero_mesa'];
        $numero_sillas = $mesa['numero_sillas'];
        $id_sala = $mesa['id_sala'];
        $imagen_sala = $mesa['imagen_sala']; // Obtención de la imagen de la sala
    }

} catch (Exception $e) {
    echo "Error al obtener los datos de la mesa: " . htmlspecialchars($e->getMessage());
    die();
}

// Consultar el nombre de la sala y tipo de sala
try {
    $sql_sala = "
        SELECT nombre_sala, tipo_sala 
        FROM tbl_salas 
        WHERE id_sala = :id_sala
    ";
    $stmt_sala = $conexion->prepare($sql_sala);
    $stmt_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    $stmt_sala->execute();
    
    $sala_info = $stmt_sala->fetch(PDO::FETCH_ASSOC);
    
    $nombre_sala = $sala_info['nombre_sala'];
    $tipo_sala = $sala_info['tipo_sala'];
} catch (Exception $e) {
    echo "Error al obtener los datos de la sala: " . htmlspecialchars($e->getMessage());
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
        <h2>Editar Mesa</h2>
        <form method="POST" action="editar.php" enctype="multipart/form-data">
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($id_mesa); ?>">
            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($id_sala); ?>">
            <input type="hidden" name="tipo_sala" value="<?php echo htmlspecialchars($tipo_sala); ?>">

            <!-- Campo para el nombre de la sala -->
            <div class="mb-3">
                <label for="nombre_sala">Nombre de la Sala</label>
                <input type="text" class="form-control" id="nombre_sala" name="nombre_sala" value="<?php echo htmlspecialchars($nombre_sala); ?>" required>
            </div>

            <!-- Campo para el número de mesa -->
            <div class="mb-3">
                <label for="numero_mesa">Número de Mesa</label>
                <input type="number" class="form-control" id="numero_mesa" name="numero_mesa" value="<?php echo htmlspecialchars($numero_mesa); ?>" required>
            </div>

            <!-- Campo para el número de sillas -->
            <div class="mb-3">
                <label for="numero_sillas">Número de Sillas</label>
                <input type="number" class="form-control" id="numero_sillas" name="numero_sillas" value="<?php echo htmlspecialchars($numero_sillas); ?>" required>
            </div>

            <!-- Campo para editar la imagen de la sala -->
            <div class="mb-3">
                <label for="imagen_sala">Imagen de la Sala</label>
                <input type="file" class="form-control" id="imagen_sala" name="imagen_sala">
                <!-- Mostrar imagen actual si existe -->
                <?php if ($imagen_sala): ?>
                    <div>
                        <p><strong>Imagen actual:</strong></p>
                        <img src="../img/<?php echo htmlspecialchars($imagen_sala); ?>" alt="Imagen de la sala" style="width: 150px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Botón para actualizar la mesa -->
            <button type="submit" class="btn btn-primary">Actualizar Recurso</button>
        </form>
        <br>
        <a href="../menu-recursos.php" class="cancelar-btn">Cancelar</a>
    </div>
</body>
</html>
