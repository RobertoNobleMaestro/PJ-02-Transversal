<?php
require_once('../php/conexion.php');
session_start();
if (!isset($_SESSION['usuario'])) {
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
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .hidden {
            display: none;
        }
        .form-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Recursos</h1>
        <form id="dynamicForm" method="POST" action="añadir.php">
    <label for="accion">Seleccionar Acción:</label>
    <select id="accion" name="accion" onchange="updateForm()">
        <option value="" selected disabled>Elige una acción</option>
        <option value="añadir_mesa">Añadir Mesa a una Sala</option>
        <option value="crear_sala">Crear Nueva Sala</option>
    </select>

    <!-- Sección para añadir una mesa -->
    <div id="form_mesa" class="hidden form-section">
        <h2>Añadir Mesa a una Sala</h2>
        <label for="sala_mesa">Sala:</label>
        <select id="sala_mesa" name="sala_mesa">
            <option value="" disabled selected>Seleccionar Sala</option>
            <?php
            foreach ($salas as $sala) {
                echo '<option value="' . htmlspecialchars($sala['id_sala']) . '">' . htmlspecialchars($sala['nombre_sala']) . '</option>';
            }
            ?>
        </select>

        <label for="numero_mesa">Número de la Mesa:</label>
        <input type="number" id="numero_mesa" name="numero_mesa" min="1" required>

        <label for="numero_sillas">Número de Sillas por Mesa:</label>
        <input type="number" id="numero_sillas" name="numero_sillas" min="1" value="4">
    </div>

    <!-- Sección para crear una nueva sala -->
    <div id="form_sala" class="hidden form-section">
        <h2>Crear Nueva Sala</h2>
        <label for="nombre_sala">Nombre de la Sala:</label>
        <input type="text" id="nombre_sala" name="nombre_sala">

        <label for="tipo_sala">Tipo de Sala:</label>
        <select id="tipo_sala" name="tipo_sala">
            <option value="" disabled selected>Selecciona un tipo</option>
            <option value="Terraza">Terraza</option>
            <option value="Comedor">Comedor</option>
            <option value="Privada">Privada</option>
        </select>

        <label for="numero_mesas">Número de Mesas:</label>
        <input type="number" id="numero_mesas" name="numero_mesas" min="1">
    </div>

    <button type="submit">Confirmar Acción</button>

    <!-- Botón Volver -->
    <button type="button" class="btn btn-danger" onclick="window.history.back();">Volver</button>
</form>

    </div>

    <script>
        function updateForm() {
            const accion = document.getElementById('accion').value;
            const formMesa = document.getElementById('form_mesa');
            const formSala = document.getElementById('form_sala');

            // Mostrar u ocultar secciones según la acción seleccionada
            if (accion === 'añadir_mesa') {
                formMesa.classList.remove('hidden');
                formSala.classList.add('hidden');
            } else if (accion === 'crear_sala') {
                formMesa.classList.add('hidden');
                formSala.classList.remove('hidden');
            } else {
                formMesa.classList.add('hidden');
                formSala.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
