<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

$id_vacuna = null;
$id_bebe = null;

// Obtener los IDs de la vacuna y del bebé de la URL
if (isset($_GET['id_vacuna'])) {
    $id_vacuna = intval($_GET['id_vacuna']);
}
if (isset($_GET['id_bebe'])) {
    $id_bebe = intval($_GET['id_bebe']);
}

// Obtener los datos de la vacunación
if ($id_vacuna) {
    $query_vacuna = "SELECT vacuna, enfermedad_previene, dosis, edad_frecuencia, fecha_aplicacion, lote FROM seguimiento_vacunacion WHERE id_vacuna = ?";
    $stmt_vacuna = $conn->prepare($query_vacuna);
    $stmt_vacuna->bind_param('i', $id_vacuna);
    $stmt_vacuna->execute();
    $result_vacuna = $stmt_vacuna->get_result();
    $vacuna_data = $result_vacuna->fetch_assoc();
}

// Manejar la actualización de la vacunación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vacuna = $_POST['vacuna'];
    $enfermedad_previene = $_POST['enfermedad_previene'];
    $dosis = $_POST['dosis'];
    $edad_frecuencia = $_POST['edad_frecuencia'];
    $fecha_aplicacion = $_POST['fecha_aplicacion'];
    $lote = $_POST['lote'];

    $query_actualizar = "UPDATE seguimiento_vacunacion SET vacuna = ?, enfermedad_previene = ?, dosis = ?, edad_frecuencia = ?, fecha_aplicacion = ?, lote = ? WHERE id_vacuna = ?";
    $stmt_actualizar = $conn->prepare($query_actualizar);
    $stmt_actualizar->bind_param('ssssssi', $vacuna, $enfermedad_previene, $dosis, $edad_frecuencia, $fecha_aplicacion, $lote, $id_vacuna);
    $stmt_actualizar->execute();

    // Redireccionar para evitar reenvío del formulario
    header("Location: seguimiento_vacunacion.php?id_bebe=" . $_POST['id_bebe']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vacunación del Bebé</title>
    <style>
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
            margin-bottom: 20px;
        }

        input[type="text"], input[type="date"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button-container {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        .button-container > * {
            flex: 1;
        }

        button, .button-container a {
            padding: 10px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
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
        <h2>Editar Vacunación del Bebé</h2>
        <form method="post" action="">
            <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
            <input type="text" name="vacuna" value="<?php echo htmlspecialchars($vacuna_data['vacuna']); ?>" required>
            <input type="text" name="enfermedad_previene" value="<?php echo htmlspecialchars($vacuna_data['enfermedad_previene']); ?>" required>
            <input type="text" name="dosis" value="<?php echo htmlspecialchars($vacuna_data['dosis']); ?>" required>
            <input type="text" name="edad_frecuencia" value="<?php echo htmlspecialchars($vacuna_data['edad_frecuencia']); ?>" required>
            <input type="date" name="fecha_aplicacion" value="<?php echo htmlspecialchars($vacuna_data['fecha_aplicacion']); ?>" required>
            <input type="text" name="lote" value="<?php echo htmlspecialchars($vacuna_data['lote']); ?>" required>
            <button type="submit">Actualizar</button>
        </form>
        <div class="button-container">
            <a href="seguimiento_vacunacion.php?id_bebe=<?php echo htmlspecialchars($id_bebe); ?>" class="button back-button">Volver</a>
        </div>
    </div>
</body>
</html>
