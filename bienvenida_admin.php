<?php
    session_start();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: iniciar_sesion.html");
        exit();
    }

    require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

    $email = $_SESSION['email'];

    // Consulta para obtener el nombre del admin
    $query_admin = "SELECT nombres FROM admin WHERE email = ?";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bind_param('s', $email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin = $result_admin->fetch_assoc();
    $nombre_admin = $admin['nombres'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Administrador</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
        }

        .welcome-container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .welcome-container h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333333;
        }

        .welcome-container p {
            font-size: 18px;
            margin-bottom: 40px;
            color: #555555;
        }

        .welcome-container img {
            width: 150px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 18px;
            border-radius: 20px;
            border: none;
            background-color: #0a84ff;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            margin: 0.5em auto;
        }

        button:hover {
            background-color: #023168;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .logout {
            background-color: red;
        }

        .logout:hover {
            background-color: #900000;
        }


    </style>
    <script>
        function logout() {
            document.getElementById('logoutForm').submit();
        }
    </script>
</head>
<body>
    <div class="welcome-container">
        <img src="babyChat.png" alt="Logo de BabyChat">
        <h1>¡Bienvenido <?php echo htmlspecialchars($nombre_admin); ?>!</h1>
        <p>¿Qué harás el día de hoy?</p>
        <button onclick="goToAdmins()">Ver administradores</button>
        <button onclick="goToPadres()">Ver padres</button>
        <form id="logoutForm" action="cerrar_sesion.php" method="POST" style="display: inline;">
            <button class="logout" id="logoutBtn" onclick="logout()">Cerrar sesión</button>
        </form>
    </div>

    <script>
        function goToAdmins() {
            window.location.href = "lista_admin.php";  // Aquí cambias a "lista_admin.php".
        }
        function goToPadres() {
            window.location.href = "lista_padres.php";  // Aquí cambias a "lista_padres.php".
        }
    </script>
</body>
</html>
