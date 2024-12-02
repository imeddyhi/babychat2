<?php
$servername = "localhost"; // Esto suele ser "localhost" en XAMPP
$username = "root"; // El usuario por defecto en XAMPP es "root"
$password = ""; // La contraseña por defecto para "root" en XAMPP es vacía
$dbname = "babychat"; // Reemplaza con el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
