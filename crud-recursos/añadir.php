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
        if ($accion === 'añadir_mesa') {
            // Añadir mesas a una sala existente
            $id_sala = htmlspecialchars($_POST['sala_mesa']);
            $numero_mesa = (int) htmlspecialchars($_POST['numero_mesa']);
            $sillas_por_mesa = (int) htmlspecialchars($_POST['numero_sillas'] ?? 4);

            // Verifica que el valor de $numero_mesa es un número mayor que 0
            if ($numero_mesa <= 0) {
                header("Location: ../menu-recursos.php?error=numero_mesa_no_valido");
                exit();
            }

            if (empty($id_sala)) {
                header("Location: ../menu-recursos.php?error=id_sala_no_seleccionada");
                exit();
            }

            if (!empty($id_sala) && $numero_mesa > 0) {
                // Verificar si el número de mesa ya existe en la sala
                $sql_check_mesa = "SELECT COUNT(*) FROM tbl_mesas WHERE id_sala = :id_sala AND numero_mesa = :numero_mesa";
                $stmt_check_mesa = $conexion->prepare($sql_check_mesa);
                $stmt_check_mesa->execute([':id_sala' => $id_sala, ':numero_mesa' => $numero_mesa]);
                $mesa_existente = $stmt_check_mesa->fetchColumn();

                if ($mesa_existente > 0) {
                    header("Location: ../menu-recursos.php?error=numero_mesa_ya_existente");
                    exit();
                }

                // Ejecutar el INSERT para la mesa
                $sql_mesa = "INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES (:numero_mesa, :id_sala, :numero_sillas, 'libre')";
                $stmt_mesa = $conexion->prepare($sql_mesa);

                // Insertar la mesa con el número especificado
                $stmt_mesa->execute([
                    ':numero_mesa' => $numero_mesa,
                    ':id_sala' => $id_sala,
                    ':numero_sillas' => $sillas_por_mesa
                ]);

                // Redirigir con éxito
                header("Location: ../menu-recursos.php?mensaje=mesa_agregada");
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
