<?php
require_once('../php/conexion.php');
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: ../index.php?error=sesion_no_iniciada");
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
    <link rel="stylesheet" href="../css/formulario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
<div class="container-form">
        <h1>Añadir Usuario</h1>
        <form method="POST" action="añadir.php" id="registrationForm">
            <label for="nombre_user">Nombre Usuario:</label>
            <input type="text" id="nombre_user" name="nombre_user" class="form-label">
            <span class="error" id="nombreUserError"></span><br><br>

            <label for="nombre_real">Nombre Real:</label>
            <input type="text" id="nombre_real" name="nombre_real" class="form-label">
            <span class="error" id="nombreRealError"></span><br><br>

            <label for="ape_usuario">Apellido:</label>
            <input type="text" id="ape_usuario" name="ape_usuario" class="form-label">
            <span class="error" id="apeUsuarioError"></span><br><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" class="form-label">
            <span class="error" id="contrasenaError"></span><br><br>

            <label for="rol_user">Rol:</label>
            <select id="rol_user" name="rol_user" class="form-label">
                <option value="" selected disabled>Selecciona un rol</option>
                <?php
                foreach ($roles as $rol) {
                    echo '<option value="' . htmlspecialchars($rol['id_rol']) . '">';
                    echo htmlspecialchars($rol['nombre_rol']);
                    echo '</option>';
                }
                ?>
            </select>
            <span class="error" id="rolUserError"></span><br><br>

            <button type="submit" class="form-button" name="btn_crear_usuario">Añadir Usuario</button>
        </form>
        <br>
        <div class="text-mid">
            <a href="../menu-admin.php" class="cancelar-btn">Cancelar</a>
        </div>
    </div>
    <script src="../js/form-añadir-usuario.js"></script>
</body>
</html>
