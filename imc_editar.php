<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

$id_imc = null;
$id_bebe = null;

if (isset($_GET['id_imc'])) {
    $id_imc = intval($_GET['id_imc']);
}
if (isset($_GET['id_bebe'])) {
    $id_bebe = intval($_GET['id_bebe']);
}

// Obtener el imc y la fecha del registro
if ($id_imc) {
    $query_imc = "SELECT imc, fecha FROM seguimiento_imc WHERE id_imc = ?";
    $stmt_imc = $conn->prepare($query_imc);
    $stmt_imc->bind_param('i', $id_imc);
    $stmt_imc->execute();
    $result_imc = $stmt_imc->get_result();
    $imc_data = $result_imc->fetch_assoc();
}

// Manejar la actualización del imc
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_imc = floatval($_POST['imc']);
    $nueva_fecha = $_POST['fecha'];

    $query_actualizar = "UPDATE seguimiento_imc SET imc = ?, fecha = ? WHERE id_imc = ?";
    $stmt_actualizar = $conn->prepare($query_actualizar);
    $stmt_actualizar->bind_param('dsi', $nuevo_imc, $nueva_fecha, $id_imc);
    $stmt_actualizar->execute();

    // Redireccionar para evitar reenvío del formulario
    header("Location: seguimiento_imc.php?id_bebe=" . $_POST['id_bebe']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar IMC del Bebé</title>
    <style>
        /* Estilos básicos para el formulario */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px; /* Espacio entre el formulario y los botones */
        }

        input[type="number"], input[type="date"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button-container {
            display: flex;
            flex-direction: row;
            gap: 10px; /* Espacio entre los botones */
        }

        .button-container > * {
            flex: 1; /* Cada botón ocupa el 50% del ancho del contenedor */
        }

        button, .button-container a {
            padding: 10px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            text-align: center;
            text-decoration: none; /* Eliminar el subrayado de los enlaces */
            display: block; /* Hacer que el enlace ocupe todo el espacio disponible */
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;  
        }

        button {
            background-color: #007bff;
            font-weight: bold;
            font-size: 12px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-button {
            background-color: #6c757d;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar IMC del Bebé</h2>
        <form method="post" action="">
            <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
            <input type="number" step="0.01" name="imc" value="<?php echo htmlspecialchars($imc_data['imc']); ?>" required>
            <input type="date" name="fecha" value="<?php echo htmlspecialchars($imc_data['fecha']); ?>" required>
            <button type="submit">Actualizar</button>
        </form>
        <div class="button-container">
            <a href="seguimiento_imc.php" class="button back-button">Volver</a>
        </div>
    </div>
</body>
</html>
