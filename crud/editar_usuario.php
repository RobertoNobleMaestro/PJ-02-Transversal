<?php
require_once('../php/conexion.php');
session_start();
// Verificar que se haya enviado el ID del usuario
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit();
}
if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}


$id_usuario = htmlspecialchars($_GET['id']);

try {
    // Consultar los datos del usuario
    $sql = "SELECT 
        u.id_usuario, 
        u.nombre_user, 
        u.nombre_real, 
        u.ape_usuario, 
        u.rol_user, 
        r.nombre_rol AS rol
    FROM tbl_usuarios u
    INNER JOIN tbl_rol r ON u.rol_user = r.id_rol 
    WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit();
    }

    // Consultar roles disponibles
    $sql_roles = "SELECT id_rol, nombre_rol FROM tbl_rol";
    $stmt_roles = $conexion->query($sql_roles);
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
    exit();
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
    <h1>Editar Usuario</h1>
    <form action="editar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>"class="form-label">

        <label for="nombre_user">Nombre Usuario:</label>
        <input type="text" id="nombre_user" name="nombre_user" value="<?php echo htmlspecialchars($usuario['nombre_user']); ?>" class="form-label"><br>

        <label for="nombre_real">Nombre Real:</label>
        <input type="text" id="nombre_real" name="nombre_real" value="<?php echo htmlspecialchars($usuario['nombre_real']); ?>" class="form-label"><br>

        <label for="ape_usuario">Apellido:</label>
        <input type="text" id="ape_usuario" name="ape_usuario" value="<?php echo htmlspecialchars($usuario['ape_usuario']); ?>" class="form-label"><br>

        <label for="rol_user">Rol:</label>
        <select id="rol_user" name="rol_user" class="form-label">
            <?php foreach ($roles as $rol) { ?>
                <option value="<?php echo htmlspecialchars($rol['id_rol']); ?>" 
                        <?php echo ($rol['id_rol'] == $usuario['rol_user']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        <button type="submit" name="btn_actualizar" class="form-button">Actualizar Usuario</button>
        <br><br>
    </form>
    <div class="text-mid">
        <a href="../menu-admin.php" class="cancelar-btn">Cancelar</a>
    </div>
    </div>

</body>
</html>
