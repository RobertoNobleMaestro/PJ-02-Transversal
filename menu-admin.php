<?php
session_start();
require_once('./php/conexion.php');

// Verificar si la variable de sesión 'Usuario' está configurada
if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}


// Obtener los filtros de la solicitud
$nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';
$rol_user = isset($_POST['rol_user']) ? $_POST['rol_user'] : '';

// Crear la consulta base
$sql = "
SELECT 
    u.id_usuario, 
    u.nombre_user, 
    u.nombre_real, 
    u.ape_usuario, 
    r.nombre_rol AS rol 
FROM tbl_usuarios u
INNER JOIN tbl_rol r ON u.rol_user = r.id_rol
WHERE 1=1";

// Agregar filtros si están presentes
if ($nombre_usuario) {
    // Modificamos la consulta para que el filtro de nombre de usuario sea más flexible
    $sql .= " AND u.nombre_user LIKE :nombre_usuario";
}
if ($rol_user && $rol_user != 'todos') {
    $sql .= " AND u.rol_user = :rol_user";
}

try {
    $stmt = $conexion->prepare($sql);
    
    // Vincular los parámetros de los filtros si están presentes
    if ($nombre_usuario) {
        // Agregar los comodines "%" para la búsqueda parcial
        $nombre_usuario_like = '%' . $nombre_usuario . '%';
        $stmt->bindParam(':nombre_usuario', $nombre_usuario_like, PDO::PARAM_STR);
    }
    if ($rol_user && $rol_user != 'todos') {
        $stmt->bindParam(':rol_user', $rol_user, PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los usuarios filtrados
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los usuarios: " . htmlspecialchars($e->getMessage());
    die(); // Detiene la ejecución en caso de error
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
                <a href="./menu-admin.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./menu-recursos.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            <div class="navbar-title">
                <h3>Lista de usuarios</h3>
            </div>

            <div class="navbar-right">
                <a href="./php/salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
        <br>
        
        <!-- Formulario de filtros -->
        <form method="POST" action="" class="mt-3">
            <div class="d-flex flex-wrap align-items-center">
                <div class="me-3 mb-3">
                    <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre de usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>">    
                </div>
                <div class="me-3 mb-3">
                <select name="rol_user" class="form-control">
                    <option value="todos" <?php echo ($rol_user == 'todos') ? 'selected' : ''; ?>>Todos</option>
                    <option value="1" <?php echo ($rol_user == '1') ? 'selected' : ''; ?>>Camarero</option>
                    <option value="2" <?php echo ($rol_user == '2') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="3" <?php echo ($rol_user == '3') ? 'selected' : ''; ?>>Gerente</option>
                    <option value="4" <?php echo ($rol_user == '4') ? 'selected' : ''; ?>>Personal de Mantenimiento</option>
                </select>
                </div>
                <div class="me-3 mb-3">
                    <button type="submit" class="btn btn-primary" style="margin-right: 1rem;">Filtrar</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href = window.location.pathname;">Borrar Filtros</button>    
                </div>
            </div>
        </form>

        <div>
            <button class="btn btn-primary" onclick="location.href='./crud/añadir_usuario.php'">Añadir Usuario</button>
        </div>
        <br>
        
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nombre Usuario</th>
                    <th>Nombre Real</th>
                    <th>Apellido</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($usuarios)) {
                    foreach ($usuarios as $usuario) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($usuario['nombre_user']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['nombre_real']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['ape_usuario']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['rol']) . '</td>';
                        echo '<td class="btn-container">';
                        echo '<a href="./crud/editar_usuario.php?id=' . urlencode($usuario['id_usuario']) . '" class="btn btn-warning">Editar</a>';
                        echo '<a href="./crud/eliminar_usuario.php?id=' . urlencode($usuario['id_usuario']) . '" class="btn btn-danger">Eliminar</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No se encontraron usuarios.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="./js/sweat_alert_usuarios.js"></script>
</body>
</html>
