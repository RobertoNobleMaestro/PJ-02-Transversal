<?php
require_once('../php/conexion.php');
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $id_mesa = htmlspecialchars($_POST['id_mesa']);
    $tipo_sala = htmlspecialchars($_POST['tipo_sala']);
    $nombre_sala = htmlspecialchars($_POST['nombre_sala']);
    $numero_mesa = htmlspecialchars($_POST['numero_mesa']);
    $numero_sillas = htmlspecialchars($_POST['numero_sillas']);
    $id_sala = htmlspecialchars($_POST['id_sala']);

    try {
        // Iniciar la transacción
        $conexion->beginTransaction();

        // Verificar que el número de mesa no exista ya en la misma sala
        $sql_check_mesa = "
            SELECT COUNT(*) 
            FROM tbl_mesas 
            WHERE id_sala = :id_sala AND numero_mesa = :numero_mesa AND id_mesa != :id_mesa
        ";
        $stmt_check_mesa = $conexion->prepare($sql_check_mesa);
        $stmt_check_mesa->execute([
            ':id_sala' => $id_sala,
            ':numero_mesa' => $numero_mesa,
            ':id_mesa' => $id_mesa
        ]);
        $mesa_existente = $stmt_check_mesa->fetchColumn();

        if ($mesa_existente > 0) {
            header('../menu-recursos.php?error=duplicado_mesa');
        }

        // Verificar que el nombre de la sala no exista ya en otra sala
        $sql_check_sala = "
            SELECT COUNT(*) 
            FROM tbl_salas 
            WHERE nombre_sala = :nombre_sala AND id_sala != :id_sala
        ";
        $stmt_check_sala = $conexion->prepare($sql_check_sala);
        $stmt_check_sala->execute([
            ':nombre_sala' => $nombre_sala,
            ':id_sala' => $id_sala
        ]);
        $sala_existente = $stmt_check_sala->fetchColumn();

        if ($sala_existente > 0) {
            header('../menu-recursos.php?error=duplicado');
        }

        // Obtener los datos actuales de la sala
        $sql_check_imagen = "SELECT imagen_sala, nombre_sala FROM tbl_salas WHERE id_sala = :id_sala";
        $stmt_check_imagen = $conexion->prepare($sql_check_imagen);
        $stmt_check_imagen->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmt_check_imagen->execute();
        $sala_info = $stmt_check_imagen->fetch(PDO::FETCH_ASSOC);

        $imagen_actual = $sala_info['imagen_sala'];
        $nombre_sala_actual = $sala_info['nombre_sala'];

        $nueva_imagen = $imagen_actual; // Por defecto, mantenemos la imagen existente

        // Si se ha subido una nueva imagen
        if (isset($_FILES['imagen_sala']) && $_FILES['imagen_sala']['error'] === UPLOAD_ERR_OK) {
            $imagen_temp = $_FILES['imagen_sala']['tmp_name'];
            $imagen_nombre = $_FILES['imagen_sala']['name'];
            $imagen_ext = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));

            // Validar la extensión
            $imagenes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imagen_ext, $imagenes_permitidas)) {
                // Generar un nuevo nombre para la imagen
                $nueva_imagen = uniqid('sala_' . $id_sala . '_') . '.' . $imagen_ext; // Generar un nombre único
                $ruta_imagen = '../img/' . $nueva_imagen;

                // Mover el archivo a la carpeta de destino
                if (move_uploaded_file($imagen_temp, $ruta_imagen)) {
                    // Eliminar la imagen anterior, si existe
                    if ($imagen_actual && file_exists('../img/' . $imagen_actual)) {
                        unlink('../img/' . $imagen_actual);
                    }
                } else {
                    throw new Exception("Error al mover la nueva imagen.");
                }
            } else {
                throw new Exception("Formato de imagen no permitido.");
            }
        }

        // Actualizar los datos de la mesa
        $sql_update_mesa = "
            UPDATE tbl_mesas 
            SET 
                numero_mesa = :numero_mesa,
                numero_sillas = :numero_sillas
            WHERE id_mesa = :id_mesa
        ";
        $stmt_update_mesa = $conexion->prepare($sql_update_mesa);
        $stmt_update_mesa->bindParam(':numero_mesa', $numero_mesa, PDO::PARAM_INT);
        $stmt_update_mesa->bindParam(':numero_sillas', $numero_sillas, PDO::PARAM_INT);
        $stmt_update_mesa->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_update_mesa->execute();

        // Actualizar los datos de la sala (nombre_sala e imagen_sala si cambian)
        if ($nueva_imagen !== $imagen_actual || $nombre_sala !== $nombre_sala_actual || isset($_FILES['imagen_sala'])) {
            $sql_update_sala = "
                UPDATE tbl_salas 
                SET 
                    nombre_sala = :nombre_sala,
                    imagen_sala = :imagen_sala
                WHERE id_sala = :id_sala
            ";
            $stmt_update_sala = $conexion->prepare($sql_update_sala);
            $stmt_update_sala->bindParam(':nombre_sala', $nombre_sala, PDO::PARAM_STR);
            $stmt_update_sala->bindParam(':imagen_sala', $nueva_imagen, PDO::PARAM_STR);
            $stmt_update_sala->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmt_update_sala->execute();
        }

        // Confirmar la transacción
        $conexion->commit();

        // Redirigir al menú con mensaje de éxito
        header("Location: ../menu-recursos.php?mensaje=recurso_actualizado");
        exit();
    } catch (Exception $e) {
        // Si ocurre un error, deshacer la transacción
        $conexion->rollBack();
        echo "Error al actualizar los datos: " . htmlspecialchars($e->getMessage());
        die();
    }
}
?>
