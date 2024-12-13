<?php
require_once('../php/conexion.php');
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

$id_mesa = htmlspecialchars($_GET['id_mesa']);
$numero_mesa = $numero_sillas = $estado = "";
$tipo_sala = $nombre_sala = $imagen_sala = "";

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

    if ($mesa) {
        $numero_mesa = $mesa['numero_mesa'];
        $numero_sillas = $mesa['numero_sillas'];
        $id_sala = $mesa['id_sala'];
        $imagen_sala = $mesa['imagen_sala'];
    }

} catch (Exception $e) {
    echo "Error al obtener los datos de la mesa: " . htmlspecialchars($e->getMessage());
    die();
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Mesa</title>
</head>
<body>
    <div class="container-form">
        <h2>Editar Mesa</h2>
        <form id="editForm" method="POST" action="editar.php" enctype="multipart/form-data">
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($id_mesa); ?>">
            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($id_sala); ?>">
            <input type="hidden" name="tipo_sala" value="<?php echo htmlspecialchars($tipo_sala); ?>">
            <div class="mb-3">
                <label for="nombre_sala">Nombre de la Sala</label>
                <input type="text" class="form-control" id="nombre_sala" name="nombre_sala" value="<?php echo htmlspecialchars($nombre_sala); ?>">
                <span id="nombreSalaError" class="error"></span>
            </div>

            <div class="mb-3">
                <label for="numero_mesa">Número de Mesa</label>
                <input type="number" class="form-control" id="numero_mesa" name="numero_mesa" value="<?php echo htmlspecialchars($numero_mesa); ?>">
                <span id="numeroMesaError" class="error"></span>
            </div>

            <div class="mb-3">
                <label for="numero_sillas">Número de Sillas</label>
                <input type="number" class="form-control" id="numero_sillas" name="numero_sillas" value="<?php echo htmlspecialchars($numero_sillas); ?>">
                <span id="numeroSillasError" class="error"></span>
            </div>

            <div class="mb-3">
                <label for="imagen_sala">Imagen de la Sala</label>
                <input type="file" class="form-control" id="imagen_sala" name="imagen_sala">
                <?php if ($imagen_sala): ?>
                    <div>
                        <p><strong>Imagen actual:</strong></p>
                        <img src="../img/<?php echo htmlspecialchars($imagen_sala); ?>" alt="Imagen de la sala" style="width: 150px; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <span id="imagenSalaError" class="error"></span>
            </div>

            <button type="submit" class="form-button">Actualizar Recurso</button>
        </form>
        <br>
        <div class="text-mid">
            <a href="../menu-recursos.php" class="cancelar-btn">Cancelar</a>
        </div>       
    </div>

    <script src="../js/validaciones-editar-mesa.js" defer></script>
</body>
</html>
