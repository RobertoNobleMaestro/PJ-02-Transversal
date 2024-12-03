<?php
session_start();
require_once('./php/conexion.php');

// Verificar si la variable de sesión 'Usuario' está configurada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar si el SweetAlert ya se mostró
if (!isset($_SESSION['sweetalert_mostrado'])) {
    $_SESSION['sweetalert_mostrado'] = false;
}
try {
    // Prepara la consulta para seleccionar todos los usuarios
    $sql = "
    SELECT 
        u.id_usuario, 
        u.nombre_user, 
        u.nombre_real, 
        u.ape_usuario, 
        u.telefono_usuario, 
        r.nombre_rol AS rol, 
        u.foto_usuario 
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
<body>
<div class="container">
        <nav class="navegacion">
            <!-- Sección izquierda con el logo grande y el ícono adicional más pequeño -->
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <!-- Título en el centro -->
            <div class="navbar-title">
                <h3>Lista de usuarios</h3>
            </div>

            <!-- Icono de logout a la derecha -->
            <div class="navbar-right">
                <a href="./php/salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Usuario</th>
                <th>Nombre Real</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Foto</th>
                <th>Acciones</th> <!-- Nueva columna para las acciones -->
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($usuarios)) {
                for ($i = 0; $i < count($usuarios); $i++) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['id_usuario']) . '</td>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['nombre_user']) . '</td>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['nombre_real']) . '</td>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['ape_usuario']) . '</td>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['telefono_usuario']) . '</td>';
                    echo '<td>' . htmlspecialchars($usuarios[$i]['rol']) . '</td>'; // Nombre del rol
                    echo '<td>';
                    if (!empty($usuarios[$i]['foto_usuario'])) {
                        echo '<img src="' . htmlspecialchars($usuarios[$i]['foto_usuario']) . '" alt="Foto" style="width:50px; height:50px;">';
                    } else {
                        echo 'Sin foto';
                    }
                    echo '</td>';
                    // Columna de acciones con enlaces para editar y eliminar
                    echo '<td>';
                    echo '<a href="./crud/editar_usuario.php?id=' . urlencode($usuarios[$i]['id_usuario']) . '">Editar</a>';
                    echo '<a href="./crud/eliminar_usuario.php?id=' . urlencode($usuarios[$i]['id_usuario']) . '">Eliminar</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr>';
                echo '<td colspan="8">No hay usuarios registrados</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    </div> 
</body>
</html>
