<?php
$mensaje_exito = "";

try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "babychat";

    if (!isset($_GET['email']) || empty($_GET['email'])) {
        throw new Exception("El parámetro 'email' no fue proporcionado en la URL.");
    }

    $email = $_GET['email'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT email, nombres FROM padres WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        throw new Exception("No se encontraron resultados para el correo proporcionado.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $deleteStmt = $conn->prepare("DELETE FROM padres WHERE email = ?");
        $deleteStmt->bind_param("s", $email);
        
        if ($deleteStmt->execute()) {
            $mensaje_exito = "Usuario eliminado con éxito";
            echo json_encode(['status' => 'success', 'message' => $mensaje_exito]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el usuario.']);
        }

        $deleteStmt->close();
        exit();
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    die();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario - BabyChat</title>
    <style>
        body {
            height: 100vh;
            margin: 0;
            font-family: sans-serif;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .Canva1 {
            background-color: #F4F5F7;
            width: 500px;
            padding: 20px;
            border-radius: 8px;
            box-sizing: border-box;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .titlle {
            text-align: center;
            font-size: 30px;
            margin-bottom: 30px;
        }

        .perfil {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            width: 80%;
            height: 120px;
            margin-left: 10%;
            border: 1px solid #ccc;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .perfil .imagen {
            margin-left: 0;
            width: 100px;
            height: 100px;
        }

        .perfil .title {
            font-size: 30px;
            text-align: center;
            margin-left: 20px;
        }

        .coment {
            text-align: center;
            margin: 10px 0; 
        }

        .button-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 13px;
        }

        button {
            width: 100%;
            padding: 6px 15px;
            border-radius: 4px;
            border: none;
            background-color: #ce0000;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;  
        }

        button:hover {
            background-color: #a00202;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .volver {
            padding: 6px 15px;
            background-color: #0a84ff;
            color: #ffffff;
            border-radius: 4px;
            text-decoration: none; /* Eliminar el subrayado de los enlaces */
            text-align: center; /* Centrar el texto dentro del enlace */
            display: block; /* Hacer que el enlace ocupe todo el espacio disponible */
            width: 100%; /* Hacer que el enlace ocupe todo el ancho del div */
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;  
        }

        .volver:hover {
            background-color: #023168;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <main>
        <div class="Canva1">
            <p class="titlle">¿Deseas eliminar este usuario?</p>

            <div class="perfil">
                <img src="LogoPerfil.png" class="imagen">
                <p class="title"><?php echo htmlspecialchars($row['nombres']); ?></p>
            </div>

            <p class="coment">Esta acción eliminará permanentemente al usuario y todos los bebés registrados en él.</p>

            <form id="deleteForm" method="POST">
                <div class="button-container">
                    <button type="submit">Eliminar</button>
                    <a href="lista_padres.php?email=<?php echo urlencode($email); ?>" class="volver">Volver</a>
                </div>
            </form>
            <p id="responseMessage" style="text-align:center; margin-top:20px;"></p> <!-- Mensaje de respuesta -->
        </div>
    </main>

    <script>
        document.getElementById('deleteForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío del formulario tradicional

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    var messageElement = document.getElementById('responseMessage');
                    if (response.status === 'success') {
                        messageElement.textContent = response.message;
                        messageElement.style.color = 'green';
                    } else {
                        messageElement.textContent = response.message;
                        messageElement.style.color = 'red';
                    }
                }
            };

            xhr.send(); // Enviar la solicitud
        });
    </script>
</body>
</html>
