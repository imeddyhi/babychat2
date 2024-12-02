<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

$id_bebe = null;

// Verificar si id_bebe está presente en la URL y es un número entero
if (isset($_GET['id_bebe']) && is_numeric($_GET['id_bebe'])) {
    $id_bebe = intval($_GET['id_bebe']);
} else {
    die('ID de bebé no válido.');
}

// Manejar la inserción de nuevos datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estatura = floatval($_POST['estatura']);
    $fecha = $_POST['fecha'];

    $query_insertar = "INSERT INTO seguimiento_estatura (id_bebe, estatura, fecha) VALUES (?, ?, ?)";
    $stmt_insertar = $conn->prepare($query_insertar);
    $stmt_insertar->bind_param('ids', $id_bebe, $estatura, $fecha);
    $stmt_insertar->execute();

    // Redireccionar para evitar reenvío del formulario
    header("Location: seguimiento_estatura.php?id_bebe=" . $id_bebe);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Estatura del Bebé</title>
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
            margin-bottom: 10px;
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
        <h2>Agregar Estatura del Bebé</h2>
        <form method="post" action="">
            <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
            <input type="number" step="0.01" name="estatura" placeholder="Estatura (cm)" required>
            <input type="date" name="fecha" required>
            <button type="submit">Agregar</button>
        </form>
        <div class="button-container">
            <a href="seguimiento_estatura.php" class="button back-button">Volver</a>
        </div>
    </div>
</body>
</html>
