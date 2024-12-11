<?php
session_start();
require_once('../php/conexion.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?error=sesiones');
    exit();
}

// Verificar que el ID del usuario a eliminar esté presente y sea válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    try {
        // Consultar el nombre del usuario para mostrar en el mensaje
        $sql_usuario = "SELECT nombre_user FROM tbl_usuarios WHERE id_usuario = :id_usuario";
        $stmt_usuario = $conexion->prepare($sql_usuario);
        $stmt_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_usuario->execute();

        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo "<p>Usuario no encontrado.</p>";
            exit();
        }

        // Si se confirma la eliminación
        if (isset($_POST['confirmar_eliminar'])) {
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->beginTransaction();

            // Eliminar datos relacionados en tbl_reservas
            $sql_reservas_recursos = "DELETE FROM tbl_reservas WHERE id_usuario = :id_usuario";
            $stmt_reservas_recursos = $conexion->prepare($sql_reservas_recursos);
            $stmt_reservas_recursos->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            if (!$stmt_reservas_recursos->execute()) {
                throw new Exception("Error al eliminar las reservas de recursos relacionadas.");
            }

            // Eliminar datos relacionados en tbl_reservas
            $sql_reservas = "DELETE FROM tbl_ocupaciones WHERE id_usuario = :id_usuario";
            $stmt_reservas = $conexion->prepare($sql_reservas);
            $stmt_reservas->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            if (!$stmt_reservas->execute()) {
                throw new Exception("Error al eliminar las reservas relacionadas.");
            }

            // Eliminar el usuario
            $sql_usuario_delete = "DELETE FROM tbl_usuarios WHERE id_usuario = :id_usuario";
            $stmt_usuario_delete = $conexion->prepare($sql_usuario_delete);
            $stmt_usuario_delete->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            if (!$stmt_usuario_delete->execute()) {
                throw new Exception("Error al eliminar el usuario.");
            }

            // Confirmar la transacción
            $conexion->commit();

            // Redirigir con un mensaje de éxito
            header('Location: ../menu-admin.php?mensaje=Usuario_eliminado_correctamente');
            exit();
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollBack();
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>ID de usuario no válido.</p>";
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
        <h1>Eliminar Usuario</h1>
        <p>¿Estás seguro de que deseas eliminar al usuario <strong><?php echo htmlspecialchars($usuario['nombre_user']); ?></strong>?</p>
        <form action="eliminar_usuario.php?id=<?php echo $id_usuario; ?>" method="post">
            <button type="submit" name="confirmar_eliminar"  class="form-button">Confirmar Eliminación</button>
            <br><br>
            <a href="../menu-admin.php" class="cancelar-btn">Cancelar</a>
        </form>
    </div>
</body>
</html>
