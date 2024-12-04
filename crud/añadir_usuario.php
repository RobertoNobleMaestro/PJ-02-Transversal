<?php
require_once('../php/conexion.php');
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}
try {
    $sql_roles = "SELECT id_rol, nombre_rol FROM tbl_rol";
    $stmt_roles = $conexion->query($sql_roles);
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los roles: " . htmlspecialchars($e->getMessage());
    die();
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Usuario</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <h1>Añadir Usuario</h1>
    <form method="POST" action="añadir.php">
        <label for="nombre_user">Nombre Usuario:</label>
        <input type="text" id="nombre_user" name="nombre_user" required><br>
        <br>
        <label for="nombre_real">Nombre Real:</label>
        <input type="text" id="nombre_real" name="nombre_real" required><br>
        <br>
        <label for="ape_usuario">Apellido:</label>
        <input type="text" id="ape_usuario" name="ape_usuario" required><br>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>
        <br>
        <label for="rol_user">Rol:</label>
        <select id="rol_user" name="rol_user" required>
            <?php
            foreach ($roles as $rol) {
                echo '<option value="' . htmlspecialchars($rol['id_rol']) . '">';
                echo htmlspecialchars($rol['nombre_rol']);
                echo '</option>';
            }
            ?>
        </select><br><br>

        <button type="submit" name="btn_crear_usuario">Añadir Usuario</button>
    </form>
</body>
</html>
