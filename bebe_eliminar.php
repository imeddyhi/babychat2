<?php
    session_start();
    if ($_SESSION['role'] !== 'padre') {
        header("Location: iniciar_sesion.html");
        exit();
    }

    require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

    $id_bebe = null;
    $email = null;

    if (isset($_GET['id_bebe'])) {
        $id_bebe = intval($_GET['id_bebe']);
    }
    if (isset($_GET['id_bebe'])) {
        $id_bebe = intval($_GET['id_bebe']);
    }

    // Obtener el bebe del registro
    if ($id_bebe) {
        $query_bebe = "SELECT nombres, estatus FROM bebes WHERE id_bebe = ?";
        $stmt_bebe = $conn->prepare($query_bebe);
        $stmt_bebe->bind_param('i', $id_bebe);
        $stmt_bebe->execute();
        $result_bebe = $stmt_bebe->get_result();
        $bebe_data = $result_bebe->fetch_assoc();
    }

/*     // Manejar la actualización del bebe (debe actualizar estatus a "E")
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombres = $_POST['nombres'];
        $estatus = $_POST['estatus'];

        $query_actualizar = "UPDATE bebes SET estatus = ? WHERE id_bebe = ?";
        $stmt_actualizar = $conn->prepare($query_actualizar);
        $stmt_actualizar->bind_param('si', $estatus, $id_bebe);
        $stmt_actualizar->execute();

        // Redireccionar para evitar reenvío del formulario
        header("Location: bebe_vista.php");
        exit();
    } */
    // Manejar la eliminación del peso
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $query_eliminar = "DELETE FROM bebes WHERE id_bebe = ?";
        $stmt_eliminar = $conn->prepare($query_eliminar);
        $stmt_eliminar->bind_param('i', $id_bebe);
        $stmt_eliminar->execute();

        // Redireccionar para evitar reenvío del formulario
        header("Location: bebe_vista.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Bebé</title>
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
        <h2>Eliminar Bebé</h2>
        <p>¿Estás seguro de que quieres eliminar el registro de tu bebé?</p>
        <form method="post" action="">
            <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
            <input type="hidden" name="nombres" value="<?php echo htmlspecialchars($nombres); ?>">
            <button type="submit">Eliminar</button>
        </form>
        <div class="button-container">
            <a href="seguimiento_peso.php" class="button back-button">Volver</a>
        </div>
    </div>
</body>
</html>
