<?php
    session_start();
    if ($_SESSION['role'] !== 'padre') {
        header("Location: iniciar_sesion.html");
        exit();
    }

    require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correcta.

    $email = $_SESSION['email'];

    // Consulta para obtener los datos del bebé
    $query_bebes = "SELECT * FROM bebes WHERE email = ?";
    $stmt_bebes = $conn->prepare($query_bebes);
    $stmt_bebes->bind_param('s', $email);
    $stmt_bebes->execute();
    $result_bebes = $stmt_bebes->get_result();

    // Consulta para obtener el nombre del padre
    $query_padre = "SELECT * FROM padres WHERE email = ?";
    $stmt_padre = $conn->prepare($query_padre);
    $stmt_padre->bind_param('s', $email);
    $stmt_padre->execute();
    $result_padre = $stmt_padre->get_result();
    $padre = $result_padre->fetch_assoc();
    $nombre_padre = $padre['nombres'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>babyChat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #1c1c1e;
            color: #ffffff;
        }

        /* Light mode styles */
        body.light-mode {
            background-color: #f5f5f5;
            color: #000000;
        }
        
        /* Side Menu styles */
        .sideMenu {
            width: 20%;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .profile-section {
            text-align: center;
        }

        .profile-header {
            display: flex;
            align-items: start;
            justify-content: center;
            background-color: #ffffff95;
            border-radius: 25px;
            padding: 0;
            margin-bottom: 30px;
            width: 120px;
            height: 120px;
            margin-left: 25%;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
        }

        .profile-header:active {
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            overflow: hidden;
        }

        /* Estilos para la selección del bebé y opciones */
        .profile-info p {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .profile-info label {
            margin: 1em auto;
            font-weight: bold;
        }

        .profile-info select {
            width: 100%;
            padding: 5px;
            margin: 5px 0 10px;
            box-sizing: border-box;
            color: var(--input-text-color);
            background-color: var(--input-bg-color);
            font-size: 14px;
            font-weight: bold;
        }

        .main-options {
            margin-top: 10px;
        }

        .main-options ul {
            list-style: none;
            padding: 0;
        }

        .main-options li {
            margin-bottom: 10px;
        }

        .main-options a {
            text-decoration: none;
            background-color: #07a1b6;
            color: #ffffff;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            margin: 2px;
        }

        .main-options a:hover {
            background-color: #025068;
            color: white;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        #logoutBtn {
            padding: 10px 18px;
            border-radius: 20px;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            background-color: #ce0000;
            margin-top: 40%;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        #logoutBtn:hover {
            background-color: #a00202;
            color: white;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Chabot styles */
        #chatbox {
            width: 80%;
            margin: 1%;
            background-color: var(--chatbox-bg-color);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        #messages {
            height: 650px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 40px;
        }

        .message {
            display: flex;
            align-items: center;
            margin: 5px;
        }

        .user-message {
            align-self: flex-end;
            background-color: #0a84ff;
            color: white;
            padding: 10px;
            border-radius: 15px;
            max-width: 70%;
        }

        .bot-message {
            align-self: flex-start;
            background-color: var(--bot-message-bg-color);
            color: var(--bot-message-text-color);
            padding: 10px;
            border-radius: 15px;
            max-width: 70%;
        }

        #userInput {
            width: calc(100% - 111px);
            padding: 10px;
            border-radius: 20px;
            border: none;
            background-color: var(--input-bg-color);
            color: var(--input-text-color);
            transition: background-color 0.3s, color 0.3s;
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

        #send:active {
            transition: transform 0.4s ease, box-shadow 0.3s ease;
            transform: scale(1.5);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Privacy Notice styles */
        .privacy-notice {
            font-size: 12px;
            color: var(--privaacy-text-color);
            text-align: center;
            margin-top: 10px;
            padding-bottom: 0;
        }

        .privacyBtn {
            color: var(--privaacy-text-color);
        }

        .privacyBtn:hover {
            color: #0a84ff;
        }

        /* CSS Variables for dark and light modes */
        body.dark-mode {
            --chatbox-bg-color: #000000;
            --bot-message-bg-color: #2c2c2e;
            --bot-message-text-color: #ffffff;
            --input-bg-color: #2c2c2e;
            --input-text-color: #ffffff;
            --privaacy-text-color: #98989c;
        }

        body.light-mode {
            --chatbox-bg-color: #ffffff;
            --bot-message-bg-color: #e5e5ea;
            --bot-message-text-color: #000000;
            --input-bg-color: #f0f0f5;
            --input-text-color: #000000;
            --privaacy-text-color: #9a9aa5;
        }
    </style>
    <script>
        function logout() {
            document.getElementById('logoutForm').submit();
        }
    </script>
</head>
<body>
    <!-- Menú lateral estático -->
    <div class="sideMenu">
        <div class="profile-section">
            <div class="profile-header">
                <img src="babyChat.png" alt="Imagen de BabyChat">
            </div>
            <!-- Botón que muestra el nombre del padre -->
            <button id="newChatBtn">+ Nuevo chat</button>
            
            
        </div>
        <div class="main-options">
            <h2>Principal</h2>
            <ul>
                <a href="seguimiento_peso.php?id_bebe="<?php echo htmlspecialchars($email); ?>>👣 Peso</a>
                <a href="seguimiento_estatura.php?id_bebe="<?php echo htmlspecialchars($email); ?>>📏 Estatura</a>
                <a href="seguimiento_imc.php?id_bebe="<?php echo htmlspecialchars($email); ?>>⚖️ IMC</a>
                <a href="seguimiento_vacunacion.php"<?php echo htmlspecialchars($email); ?>>💉 Vacunación</a>
                <a href="historial_chats.php"<?php echo htmlspecialchars($email); ?>>💬 Chats</a>
            </ul>
        </div>
        <div>
            <h2>Configuración</h2>
            <div class="profile-info">
                <a id="addBabyBtn" href="bebe_vista.php?id_bebe="<?php echo htmlspecialchars($email); ?>><button>Bebés</button></a>
                <a id="addBabyBtn" href="bebe_agregar.php"><button>Agregar bebé</button></a>
                <a href="padre_perfil.php?email=<?php echo htmlspecialchars($email); ?>"><button>Perfil de <?php echo htmlspecialchars($nombre_padre); ?></button></a>
            </div>
        </div>
        <form id="logoutForm" action="cerrar_sesion.php" method="POST" style="display: inline;">
            <button class="logout" id="logoutBtn" onclick="logout()">Cerrar sesión</button>
        </form>
    </div>

    <!-- babyChatbot -->
    <div id="chatbox">
        <div id="messages"></div>
        <input type="text" id="userInput" placeholder="Escribe tu pregunta aquí...">
        <button id="send" onclick="sendMessage()">Enviar ↑</button>
        <div class="privacy-notice">BabyChat puede producir información inexacta, los chats serán monitoreados por personas que supervisan las respuestas para mejorar su efectividad. <a class="privacyBtn" href="privacidad.html">Privacy Notice</a></div>
    </div>

    <!-- Script de conexión con Flowise -->
    <script>
        const apiUrl = "http://localhost:3000/api/v1/prediction/19c01520-1822-417f-b9ec-d5950b77b4aa";
        const apiKey = "PfCMOoJsbCbCL8jdEA9wIexQ7RrCcl4K76225cWbfmU";  // Reemplaza con tu clave de API

        function sendMessage() {
            const userInput = document.getElementById("userInput").value;
            if (userInput) {
                const messagesDiv = document.getElementById("messages");

                // Agregar mensaje del usuario al chat
                const userMessage = document.createElement("div");
                userMessage.className = "message user-message";
                userMessage.innerHTML = `${userInput}`;
                messagesDiv.appendChild(userMessage);

                // Preparar los datos de la solicitud
                const data = {
                    question: userInput
                };

                // Realizar la solicitud a la API de Flowise
                fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${apiKey}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    const botMessage = document.createElement("div");
                    botMessage.className = "message bot-message";
                    
                    // Asegúrate de acceder a la propiedad 'text' en el objeto de la respuesta
                    botMessage.innerHTML = `${data.text}`;
                    
                    // Agregar el mensaje del bot y el botón de guardar
                    const saveButton = document.createElement("button");
                    saveButton.innerText = "Guardar pregunta y respuesta";
                    saveButton.onclick = function() {
                        saveChat(userInput, data.text);
                    };
                    messagesDiv.appendChild(botMessage);
                    messagesDiv.appendChild(saveButton);

                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                })
                .catch(error => console.error("Error:", error));

                // Limpiar la entrada del usuario
                document.getElementById("userInput").value = '';
            }
        }

        function saveChat(question, answer) {
            // Usar fetch para enviar la pregunta y respuesta al servidor
            fetch('guardar_chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question: question,
                    answer: answer,
                    email: '<?php echo $email; ?>'
                })
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

    <!-- Detecta el tema del sistema y lo aplica -->
    <script>
        function applyTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark-mode');
                document.body.classList.remove('light-mode');
            } else {
                document.body.classList.add('light-mode');
                document.body.classList.remove('dark-mode');
            }
        }

        // Aplica el tema en la página cargada
        applyTheme();

        // Escuchará a los cambios de tema del sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    </script>
</body>
</html>
