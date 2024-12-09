<?php
session_start();
require_once('./php/conexion.php');

// Verificar si la variable de sesión 'Usuario' está configurada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

try {
    // Prepara la consulta para seleccionar todos los usuarios
    $sql = "
    SELECT 
        u.id_usuario, 
        u.nombre_user, 
        u.nombre_real, 
        u.ape_usuario, 
        r.nombre_rol AS rol 
    FROM tbl_usuarios u
    INNER JOIN tbl_rol r ON u.rol_user = r.id_rol";    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    // Fetch all rows as an associative array
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los usuarios: " . htmlspecialchars($e->getMessage());
    die(); // Detiene la ejecución en caso de error
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
<style>
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .tabla-container {
            margin-top: 20px;
        }
        h2 {
            margin-top: 40px;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
    </style>
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
        <div>
            <button class="btn btn-primary" onclick="location.href='./crud/añadir_usuario.php'">Añadir Usuario</button>
        </div>
        <br>
        <table class='table'>
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
                    echo '<td>';
                    echo '<a href="./crud/editar_usuario.php?id=' . urlencode($usuario['id_usuario']) . '" class="btn btn-warning">Editar</a>';
                    echo '<a href="./crud/eliminar_usuario.php?id=' . urlencode($usuario['id_usuario']) . '" class="btn btn-danger">Eliminar</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">No hay usuarios registrados</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div> 
</body>
</html>
