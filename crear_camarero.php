<?php
    require_once './php/conexion.php';
    mysqli_autocommit($conexion, false); 
    session_start();  
    
    if (isset($_POST['btn_crear_usuario'])) {
        $usuario = isset($_POST['Usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_POST['Usuario'])) : '';
        $apellido = isset($_POST['apellido']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_POST['apellido'])) : '';
        $contrasena = isset($_POST['Contra']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_POST['Contra'])) : '';
        if (empty($usuario) || empty($contrasena) || empty($apellido)) {
            header('Location: camarero.php?error=campos_vacios');
            exit();
        }
        if (strlen($usuario) < 3 || !is_string($usuario)) {
            header('Location: camarero.php?error=usuario_invalido');
            exit();
        }
        if (strlen($apellido) < 3 || !is_string($apellido)) {
            header('Location: camarero.php?error=apellido_invalido');
            exit();
        }
        if (strlen($contrasena) < 8) {
            header('Location: camarero.php?error=contrasena_invalida');
            exit();
        }
        $contrasena_encriptada = password_hash($contrasena, PASSWORD_BCRYPT);
        
        $sql = "SELECT * FROM tbl_usuarios WHERE nombre_user = ? AND apellido_user = ?";
        $stmt = mysqli_stmt_init($conexion);
        
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $usuario, $apellido);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($resultado) > 0) {
                header('Location: camarero.php?error=usuario_invalido');
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            throw new Exception("Error al preparar la consulta.");
        }
        
        try {
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
             
            $sql = "INSERT INTO tbl_usuarios (nombre_user, apellido_user, contrasena) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($conexion);
            
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $usuario, $apellido, $contrasena_encriptada);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_commit($conexion);
                header("Location: camarero.php");
                exit();
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
        }
        mysqli_close($conexion);
    }
?>
