<?php
require_once('../php/conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?error=sesion_no_iniciada");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = htmlspecialchars($_POST['accion']);

    try {
        if ($accion === 'crear_sala') {
            // Crear una nueva sala con mesas predeterminadas
            $nombre_sala = htmlspecialchars($_POST['nombre_sala']);
            $tipo_sala = htmlspecialchars($_POST['tipo_sala']);
            $numero_mesas = htmlspecialchars($_POST['numero_mesas']);
            $sillas_por_mesa = 4; 
            $nombre_imagen = ''; 

            // Verificar si se ha subido una imagen
            if (isset($_FILES['imagen_sala']) && $_FILES['imagen_sala']['error'] === UPLOAD_ERR_OK) {
                $imagen_temp = $_FILES['imagen_sala']['tmp_name'];
                $imagen_nombre = $_FILES['imagen_sala']['name'];
                $imagen_ext = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));

                // Validar la extensión de la imagen
                $imagenes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imagen_ext, $imagenes_permitidas)) {
                    // Generar un nombre único para la imagen en el directorio actual (no cambiar el nombre original)
                    $nombre_imagen = $imagen_nombre; // Aquí estamos guardando el nombre original de la imagen

                    // Especificar el directorio de destino donde se guardará la imagen
                    $directorio_destino = '../img/' . $nombre_imagen;
                    
                    // Mover la imagen al directorio de destino
                    if (!move_uploaded_file($imagen_temp, $directorio_destino)) {
                        echo "Error al subir la imagen.";
                        exit();
                    }
                } else {
                    echo "Solo se permiten imágenes con los siguientes formatos: jpg, jpeg, png, gif.";
                    exit();
                }
            }

            if (!empty($nombre_sala) && !empty($tipo_sala) && !empty($numero_mesas)) {
                // Insertar la sala en la base de datos (guardar solo el nombre de la imagen)
                $sql_sala = "INSERT INTO tbl_salas (nombre_sala, tipo_sala, imagen_sala) VALUES (:nombre_sala, :tipo_sala, :imagen_sala)";
                $stmt_sala = $conexion->prepare($sql_sala);
                $stmt_sala->execute([
                    ':nombre_sala' => $nombre_sala,
                    ':tipo_sala' => $tipo_sala,
                    ':imagen_sala' => $nombre_imagen // Guardar solo el nombre de la imagen en la base de datos
                ]);

                // Obtener el ID de la sala recién creada
                $id_sala = $conexion->lastInsertId();

                // Insertar mesas asociadas a la nueva sala
                $sql_mesa = "INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas) VALUES (:numero_mesa, :id_sala, :numero_sillas, 'libre')";
                $stmt_mesa = $conexion->prepare($sql_mesa);

                for ($i = 1; $i <= $numero_mesas; $i++) {
                    $numero_mesa = $id_sala * 100 + $i; // Ejemplo: Sala 1 -> Mesas 101, 102...
                    $stmt_mesa->execute([
                        ':numero_mesa' => $numero_mesa,
                        ':id_sala' => $id_sala,
                        ':numero_sillas' => $sillas_por_mesa
                    ]);
                }

                header("Location: ../menu-recursos.php?mensaje=sala_creada");
                exit();
            } else {
                header("Location: ../menu-recursos.php?error=datos_incompletos");
                exit();
            }

        } elseif ($accion === 'añadir_mesa') {
            // Añadir mesas a una sala existente
            $id_sala = htmlspecialchars($_POST['sala_mesa']);
            $numero_mesas = htmlspecialchars($_POST['numero_mesa']);
            $sillas_por_mesa = htmlspecialchars($_POST['numero_sillas'] ?? 4);

            if (!empty($id_sala) && !empty($numero_mesas)) {
                // Obtener el último número de mesa de la sala seleccionada
                $sql_max_mesa = "SELECT MAX(numero_mesa) as max_mesa FROM tbl_mesas WHERE id_sala = :id_sala";
                $stmt_max_mesa = $conexion->prepare($sql_max_mesa);
                $stmt_max_mesa->execute([':id_sala' => $id_sala]);
                $max_mesa = $stmt_max_mesa->fetch(PDO::FETCH_ASSOC)['max_mesa'] ?? ($id_sala * 100);

                $sql_mesa = "INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas) VALUES (:numero_mesa, :id_sala, :numero_sillas, 'libre')";
                $stmt_mesa = $conexion->prepare($sql_mesa);

                for ($i = 1; $i <= $numero_mesas; $i++) {
                    $numero_mesa = $max_mesa + $i;
                    $stmt_mesa->execute([
                        ':numero_mesa' => $numero_mesa,
                        ':id_sala' => $id_sala,
                        ':numero_sillas' => $sillas_por_mesa
                    ]);
                }

                header("Location: ../menu-recursos.php?mensaje=mesas_agregadas");
                exit();
            } else {
                header("Location: ../menu-recursos.php?error=datos_incompletos");
                exit();
            }

        } else {
            header("Location: ../menu-recursos.php?error=accion_no_valida");
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
        die();
    }
}
?>
