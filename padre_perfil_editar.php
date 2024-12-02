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

    $stmt = $conn->prepare("SELECT email, nombres, primer_apellido, segundo_apellido, fecha_nacimiento, genero, estatus, fecha_registro FROM padres WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        throw new Exception("No se encontraron resultados para el correo proporcionado.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombres = $_POST['nombres'];
        $primer_apellido = $_POST['primer_apellido'];
        $segundo_apellido = !empty($_POST['segundo_apellido']) ? $_POST['segundo_apellido'] : null;
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $genero = $_POST['genero'];

        $updateStmt = $conn->prepare("UPDATE padres SET nombres = ?, primer_apellido = ?, segundo_apellido = ?, fecha_nacimiento = ?, genero = ? WHERE email = ?");
        $updateStmt->bind_param("ssssss", $nombres, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $genero, $email);
        
        if ($updateStmt->execute()) {
            $mensaje_exito = "¡Guardado con éxito!";
            $row['nombres'] = $nombres;
            $row['primer_apellido'] = $primer_apellido;
            $row['segundo_apellido'] = $segundo_apellido;
            $row['fecha_nacimiento'] = $fecha_nacimiento;
            $row['genero'] = $genero;
        }

        $updateStmt->close();
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Perfil - BabyChat</title>
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

            main {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            .box {
                background-color: var(--box-bg-color);
                width: 500px;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .header {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
            }

            .header .title {
                font-size: 30px;
                text-align: center;
                margin-left: 0;
                margin-right: 30px; /* Espacio entre la imagen y el título */
            }

            .profile-header {
                margin-left: 30px;
                display: flex;
                align-items: start;
                justify-content: center;
                background-color: #ffffff95;
                border-radius: 25px;
                padding: 0;
                width: 120px;
                height: 120px;
                transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            }

            .profile-header img {
                width: 120px;
                height: 120px;
            }

            form {
                display: flex;
                flex-direction: column;
            }

            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr; /* Dos columnas de igual ancho */
                gap: 15px; /* Espacio entre columnas */
            }

            .form-item {
                display: flex;
                flex-direction: column;
            }

            .form-item label {
                font-size: 15px;
                margin-bottom: 5px;
            }

            .form-item input,
            .form-item select {
                width: 100%;
                padding: 8px;
                color: var(--input-text-color);
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                background-color: var(--input-bg-color);
            }

            /* Campos que ocupan el ancho completo */
            .form-item.full-width {
                grid-column: span 2;
            }

            .button-container {
                margin-top: 30px;
                display: flex;
                justify-content: space-between;
            }
            .form-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
            }

            button {
                padding: 10px 18px;
                border-radius: 20px;
                border: none;
                background-color: #019733;
                color: white;
                cursor: pointer;
                font-weight: bold;
                font-size: 12px;
                transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;

            }

            button:hover {
                background-color: #026824;
                transform: scale(0.95);
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            }

            button.volver {
                background-color: #0a84ff;
            }

            button.volver:hover {
                background-color: #023168;
            }

            .cuenta {
                margin-top: 20px;
                font-size: 15px;
                text-align: center;
            }
            .inicia {
                margin-top: 20px;
                font-size: 15px;
                color: #515c6d;
                text-align: center;
                text-decoration: none;
            }
            .inicia:hover {
                color: #8198bb;
            }

            /* CSS Variables for dark and light modes */
            body.dark-mode {
                --box-bg-color: #000000;
                --input-bg-color: #2c2c2e;
                --input-text-color: #ffffff;
            }

            body.light-mode {
                --box-bg-color: #ffffff;
                --input-bg-color: #f0f0f5;
                --input-text-color: #000000;
            }
        </style>
    </head>
    <body>
        <main>
            <div class="box">
                <div class="header">
                    <p class="title">Editar Perfil</p>
                    <div class="profile-header">
                        <img src="babyChat.png" class="imagen">
                    </div>
                </div>
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-item">
                            <label for="nombres">Nombres:</label>
                            <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($row['nombres']); ?>" required>
                        </div>
                        <div class="form-item">
                            <label for="primer_apellido">Primer apellido:</label>
                            <input type="text" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($row['primer_apellido']); ?>" required>
                        </div>
                        <div class="form-item">
                            <label for="segundo_apellido">Segundo apellido (opcional):</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($row['segundo_apellido']); ?>">
                        </div>
                        <div class="form-item">
                            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($row['fecha_nacimiento']); ?>" required>
                        </div>
                        <div class="form-item">
                            <label for="genero">Género:</label>
                            <input type="text" id="genero" name="genero" value="<?php echo htmlspecialchars($row['genero']); ?>" placeholder="M, F, Otro" required>
                            <?php if (!empty($mensaje_exito)): ?>
                                <span class="success-message"><?php echo $mensaje_exito; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="submit">Guardar Cambios</button>
                        <button type="button" class="volver" onclick="window.location.href='chat.php?email=<?php echo urlencode($email); ?>'">Volver</button>
                    </div>
                </form>
            </div>
        </main>
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
