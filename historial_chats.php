<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php';

// Obtener el email del usuario que inició sesión
$email_usuario = $_SESSION['email'];

// Consulta para obtener los chats vinculados al email del usuario
$query_chats = "SELECT id_chat, nombre_chat, fecha_inicio FROM chats WHERE email = ?";
$stmt_chats = $conn->prepare($query_chats);
$stmt_chats->bind_param('s', $email_usuario);
$stmt_chats->execute();
$result_chats = $stmt_chats->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Chats</title>
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
        <h2 class="header-title">Historial de Chats</h2>

        <?php if ($result_chats->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Chat</th>
                        <th>Fecha de Inicio</th>
                        <th>Ver pregunta y respuesta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_chats->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre_chat']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                            <td>
                                <a href="vista_mensaje.php?id_chat=<?php echo htmlspecialchars($row['id_chat']); ?>"><button>Ver</button></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron chats para este usuario.</p>
        <?php endif; ?>

        <a href="chat.php"><button>Iniciar Nuevo Chat</button></a>
    </div>
</body>
</html>

<?php
$stmt_chats->close();
$conn->close();
?>
