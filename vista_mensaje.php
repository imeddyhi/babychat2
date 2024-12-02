<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php';

// Verificar si se ha recibido el id_chat
if (isset($_GET['id_chat'])) {
    $id_chat = $_GET['id_chat'];

    // Consulta para obtener los mensajes relacionados con el id_chat
    $query_mensajes = "SELECT pregunta, respuesta, fecha FROM mensajes WHERE id_chat = ?";
    $stmt_mensajes = $conn->prepare($query_mensajes);
    $stmt_mensajes->bind_param('s', $id_chat);
    $stmt_mensajes->execute();
    $result_mensajes = $stmt_mensajes->get_result();
} else {
    // Si no se recibe el id_chat, redirigir a la página de historial de chats
    header("Location: historial_chats.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Chat</title>
    <style>
        /* Estilos similares a los de tu archivo proporcionado */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
            background-color: #f5f5f5;
            color: #000;
        }

        .box {
            background-color: var(--box-bg-color);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 900px;
            max-width: 90%;
        }

        .header-title {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--table-bg-color);
            color: var(--table-text-color);
        }

        td {
            background-color: var(--table-bg-color);
            color: var(--table-text-color);
        }

        button {
            padding: 10px 18px;
            border-radius: 20px;
            border: none;
            background-color: #0a84ff;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #025068;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="box">
        <h2 class="header-title">Detalles del Chat</h2>

        <?php if ($result_mensajes->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_mensajes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['pregunta']); ?></td>
                            <td><?php echo htmlspecialchars($row['respuesta']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron mensajes para este chat.</p>
        <?php endif; ?>

        <a href="historial_chats.php"><button>Volver al Historial de Chats</button></a>
    </div>
</body>
</html>

<?php
$stmt_mensajes->close();
$conn->close();
?>
