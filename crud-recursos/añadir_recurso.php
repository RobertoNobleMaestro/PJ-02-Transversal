    <?php
    require_once('../php/conexion.php');
    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['rol_user'] != "2") {
        header("Location: ../index.php?error=sesion_no_iniciada");
        exit();
    }
    try {
        // Consultar las salas existentes
        $sql_salas = "SELECT id_sala, nombre_sala FROM tbl_salas";
        $stmt_salas = $conexion->query($sql_salas);
        $salas = $stmt_salas->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error al obtener las salas: " . htmlspecialchars($e->getMessage());
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
        <h1>Gestión de Recursos</h1>
        <form id="dynamicForm" method="POST" action="añadir.php" enctype="multipart/form-data">
            <label for="accion">Seleccionar Acción:</label>
            <select id="accion" name="accion" class="form-label">
                <option value="" selected disabled>Elige una acción</option>
                <option value="añadir_mesa">Añadir Mesa a una Sala</option>
                <option value="crear_sala">Crear Nueva Sala</option>
            </select>
            <span id="accionError" class="error"></span><br><br>

            <!-- Sección para añadir una mesa -->
            <div id="form_mesa" class="hidden form-section">
                <h2>Añadir Mesa a una Sala</h2>
                <label for="sala_mesa">Sala:</label>
                <select id="sala_mesa" name="sala_mesa" class="form-label">
                    <option value="" disabled selected>Seleccionar Sala</option>
                    <?php
                    foreach ($salas as $sala) {
                        echo '<option value="' . htmlspecialchars($sala['id_sala']) . '">' . htmlspecialchars($sala['nombre_sala']) . '</option>';
                    }
                    ?>
                </select>
                <span id="salaMesaError" class="error"></span><br><br>
                    
                <label for="numero_mesa">Número de mesas que quieres agregar:</label>
                <input type="number" id="numero_mesa" name="numero_mesa" class="form-label">
                <span id="numeroMesaError" class="error"></span><br><br>

                <label for="numero_sillas">Número de Sillas por Mesa:</label>
                <input type="number" id="numero_sillas" name="numero_sillas" value="4" class="form-label">
                <span id="numeroSillasError" class="error"></span><br><br>
            </div>

            <!-- Sección para crear una nueva sala -->
            <div id="form_sala" class="hidden form-section">
                <h2>Crear Nueva Sala</h2>
                <label for="nombre_sala">Nombre de la Sala:</label>
                <input type="text" id="nombre_sala" name="nombre_sala" class="form-label">
                <span id="nombreSalaError" class="error"></span><br><br>

                <label for="tipo_sala">Tipo de Sala:</label>
                <select id="tipo_sala" name="tipo_sala" class="form-label">
                    <option value="" disabled selected>Selecciona un tipo</option>
                    <option value="Terraza">Terraza</option>
                    <option value="Comedor">Comedor</option>
                    <option value="Privada">Privada</option>
                </select>
                <span id="tipoSalaError" class="error"></span><br><br>

                <label for="numero_mesas">Número de Mesas:</label>
                <input type="number" id="numero_mesas" name="numero_mesas" class="form-label">
                <span id="numeroMesasError" class="error"></span><br><br>

                <!-- Campo para cargar la imagen de la sala -->
                <label for="imagen_sala">Imagen de la Sala:</label>
                <input type="file" class="form-control" id="imagen_sala" name="imagen_sala" accept="image/*">
                <span id="imagenSalaError" class="error"></span><br><br>
            </div>

            <button class="form-button" type="submit">Confirmar Acción</button>
            <br><br>
            <div class="text-mid">
                <a href="../menu-recursos.php" class="cancelar-btn">Cancelar</a>
            </div>   
        </form>
        <script src="../js/validaciones-recursos.js"></script>
    </div>
    </body>
    </html>
