<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Listado de camareros</title>
</head>
<body>
<?php
session_start();
include_once('./php/conexion.php');
?>

<body id="body">
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br>      
    <div class="container"> 
        <div class="left-side"> 
            <img src="./img/logo.png" alt="Logo"> 
        </div>
        <div class="right-side">
            <form action="crear_camarero.php" method="post" class="form-login">
                <h2>Crear usuario</h2> 
                <label for="Usuario">Usuario: </label> 
                <br><br>
                <input type="text" name="Usuario" id="Usuario" class="form-login-label"> 
                <span id="usuarioError" class="error-message"></span> 
                <br><br>
                <label for="apellido">Apellido: </label> 
                <br><br>
                <input type="text" name="apellido" id="apellido" class="form-login-label"> 
                <span id="apellidoError" class="error-message"></span> 
                <br><br>
                <label for="Contra">Contraseña: </label> 
                <br><br>
                <input type="password" name="Contra" id="Contra" class="form-login-label"> 
                <span id="contraError" class="error-message"></span> 
                <br>
                <button type="submit" name="btn_crear_usuario" class="form-login-button">Crear usuario</button>
            </form>
        </div>
    </div>      
    <script src="./js/auth.js"></script>
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
        }
    </style>
    <br>
    <?php
   $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'nombre_user ASC';
   if (isset($_POST['query_apellido']) || isset($_POST['query_usuarios'])) {
       $query_apellido = mysqli_real_escape_string($conexion, $_POST['query_apellido']);
       $query_usuarios = mysqli_real_escape_string($conexion, $_POST['query_usuarios']);
       
       try {
           $sql_usuarios = "SELECT * FROM tbl_usuarios WHERE nombre_user LIKE '%$query_usuarios%' AND apellido_user LIKE '%$query_apellido%' ORDER BY $order_by";
           $stmt = mysqli_prepare($conexion, $sql_usuarios);
           mysqli_stmt_execute($stmt);
           $resultado = mysqli_stmt_get_result($stmt);
           $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
           if (count($usuarios) > 0) {
                echo "<table class='table table-striped'>
                <thead>
                <tr class='thead-dark'>
                    <th>Camarero</th>
                    <th>Apellido</th>
                <th>Contraseña</th>
                </tr></thead>";
               foreach ($usuarios as $fila) {
                   echo "<tr>";
                   echo "<td>" . $fila['nombre_user'] . "</td>";
                   echo "<td>" . $fila['apellido_user'] . "</td>";
                   echo "<td>" . $fila['contrasena'] . "</td>";
                   echo "</tr>";
               }
               echo "</table>";
           } else {
               echo "No hay usuarios con ese apellido y nombre de usuario.";
           }
           mysqli_stmt_close($stmt);
           mysqli_close($conexion);
   
       } catch (Exception $e) {
           echo "Error: " . $e->getMessage();
       }
   } else {
       try {
           $sql = "SELECT * FROM tbl_usuarios ORDER BY $order_by";
           $stmt = mysqli_prepare($conexion, $sql);
           mysqli_stmt_execute($stmt);
           $resultado = mysqli_stmt_get_result($stmt);
   
           echo "<table table table-striped>
           <thead>
           <tr class='thead-dark'>
               <th>Camarero</th>
               <th>Apellido</th>
              <th>Contraseña</th>
           </tr></thead>";
           while ($fila = mysqli_fetch_assoc($resultado)) {
               echo "<tr>";
               echo "<td>" . $fila['nombre_user'] . "</td>";
               echo "<td>" . $fila['apellido_user'] . "</td>";
               echo "<td>" . $fila['contrasena'] . "</td>";
               echo "</tr>";
           }
           echo "</table>";
           mysqli_stmt_close($stmt);
           mysqli_close($conexion);
       } catch (Exception $e) {
           echo "Error: " . $e->getMessage();
       }
   }
   ?>   
   <br>
   <br>
    <div class="container"> 
        <div class="left-side"> 
        <form action="" method="get" class="form-login">
            <button type="submit" name="order_by" value="apellido_user ASC" class="form-login-button">Ordenar por Apellido Ascendente</button>
            <br>
            <br>
            <button type="submit" name="order_by" value="apellido_user DESC" class="form-login-button">Ordenar por Apellido Descendente</button>
        </form>        
        </div>
        <div class="right-side">
        <form action="" method="post" class="form-login">
            <label for="Usuario">Usuario: </label>
            <br><br>
            <input type="text" name="query_usuarios" id="Usuario" class="form-login-label">
            <br>
            <br>
            <label for="Apellido">Apellido: </label>
            <br><br>
            <input type="text" name="query_apellido" id="Apellido" class="form-login-label">
            <br>
            <br>
            <button type="submit" class="form-login-button">Buscar</button>
        </form>
        </div>
    </div>      
    <br>
    <script src="./js/auth.js"></script>
</body>
</html>