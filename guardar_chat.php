<?php
require 'conexion.php'; // Conexión a la base de datos

// Obtener los datos de la solicitud POST
$data = json_decode(file_get_contents("php://input"), true);

$question = $data['question'];
$answer = $data['answer'];
$email = $data['email'];

// Guardar en la tabla 'chats'
$query_chat = "INSERT INTO chats (nombre_chat, fecha_inicio, email) VALUES (?, NOW(), ?)";
$stmt_chat = $conn->prepare($query_chat);
$stmt_chat->bind_param('ss', $question, $email);
$stmt_chat->execute();

$id_chat = $stmt_chat->insert_id; // Obtener el ID del chat insertado

// Guardar en la tabla 'mensajes'
$query_mensaje = "INSERT INTO mensajes (pregunta, respuesta, fecha, id_chat) VALUES (?, ?, NOW(), ?)";
$stmt_mensaje = $conn->prepare($query_mensaje);
$stmt_mensaje->bind_param('ssi', $question, $answer, $id_chat);
$stmt_mensaje->execute();

echo "Pregunta y respuesta guardadas con éxito!";
?>
