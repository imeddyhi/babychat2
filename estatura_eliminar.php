<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

$id_estatura = null;
$id_bebe = null;

if (isset($_GET['id_estatura'])) {
    $id_estatura = intval($_GET['id_estatura']);
}
if (isset($_GET['id_bebe'])) {
    $id_bebe = intval($_GET['id_bebe']);
}

// Obtener el estatura y la fecha del registro
if ($id_estatura) {
    $query_estatura = "SELECT estatura, fecha FROM seguimiento_estatura WHERE id_estatura = ?";
    $stmt_estatura = $conn->prepare($query_estatura);
    $stmt_estatura->bind_param('i', $id_estatura);
    $stmt_estatura->execute();
    $result_estatura = $stmt_estatura->get_result();
    $estatura_data = $result_estatura->fetch_assoc();
}

// Manejar la eliminación del estatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query_eliminar = "DELETE FROM seguimiento_estatura WHERE id_estatura = ?";
    $stmt_eliminar = $conn->prepare($query_eliminar);
    $stmt_eliminar->bind_param('i', $id_estatura);
    $stmt_eliminar->execute();

    // Redireccionar para evitar reenvío del formulario
    header("Location: seguimiento_estatura.php?id_bebe=" . $_POST['id_bebe']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Estatura del Bebé</title>
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
            background-color: #dc3545; /* Color rojo para el botón de eliminar */
            font-weight: bold;
            font-size: 12px;
        }

        button:hover {
            background-color: #c82333;
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
        <h2>Eliminar Estatura del Bebé</h2>
        <p>¿Estás seguro de que quieres eliminar el registro de estatura de este bebé?</p>
        <form method="post" action="">
            <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
            <input type="hidden" name="id_estatura" value="<?php echo htmlspecialchars($id_estatura); ?>">
            <button type="submit">Eliminar</button>
        </form>
        <div class="button-container">
            <a href="seguimiento_estatura.php" class="button back-button">Volver</a>
        </div>
    </div>
</body>
</html>
