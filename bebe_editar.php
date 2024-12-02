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
        $query_bebe = "SELECT nombres, primer_apellido, segundo_apellido, fecha_nacimiento, genero, discapacidad, alergias, enfermedades FROM bebes WHERE id_bebe = ?";
        $stmt_bebe = $conn->prepare($query_bebe);
        $stmt_bebe->bind_param('i', $id_bebe);
        $stmt_bebe->execute();
        $result_bebe = $stmt_bebe->get_result();
        $bebe_data = $result_bebe->fetch_assoc();
    }

    // Manejar la actualización del bebe
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombres = $_POST['nombres'];
        $primer_apellido = $_POST['primer_apellido'];
        $segundo_apellido = !empty($_POST['segundo_apellido']) ? $_POST['segundo_apellido'] : null;
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $genero = $_POST['genero'];
        $discapacidad = !empty($_POST['discapacidad']) ? $_POST['discapacidad'] : null;
        $alergias = !empty($_POST['alergias']) ? $_POST['alergias'] : null;
        $enfermedades = !empty($_POST['enfermedades']) ? $_POST['enfermedades'] : null;

        $query_actualizar = "UPDATE bebes SET nombres = ?, primer_apellido = ?, segundo_apellido = ?, fecha_nacimiento = ?, genero = ?, discapacidad = ?, alergias = ?, enfermedades = ? WHERE id_bebe = ?";
        $stmt_actualizar = $conn->prepare($query_actualizar);
        $stmt_actualizar->bind_param('ssssssssi', $nombres, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $genero, $discapacidad, $alergias, $enfermedades, $id_bebe);
        $stmt_actualizar->execute();

        // Redireccionar para evitar reenvío del formulario
        header("Location: bebe_vista.php?id_bebe=" . $_POST['id_bebe']);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Bebé en BabyChat</title>
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
                <h1 class="header-title">Edita la información de tu bebé</h1>
                <div class="profile-header">
                    <img src="babyChat.png" alt="Imagen de BabyChat">
                </div>
            </div>
            <form action="" method="post">
                <div class="form-grid">
                    <input type="hidden" name="id_bebe" value="<?php echo htmlspecialchars($id_bebe); ?>">
                    <div class="form-item">
                        <label for="nombres">Nombres:</label>
                        <input type="text" id="nombres" name="nombres" class="text" value="<?php echo htmlspecialchars($bebe_data['nombres']); ?>" required>
                    </div>
                    
                    <div class="form-item">
                        <label for="primer_apellido">Primer apellido:</label>
                        <input type="text" id="primer_apellido" name="primer_apellido" class="txt" value="<?php echo htmlspecialchars($bebe_data['primer_apellido']); ?>" placeholder="Opcional">
                    </div>
                    <div class="form-item">
                        <label for="segundo_apellido">Segundo apellido:</label>
                        <input type="text" id="segundo_apellido" name="segundo_apellido" class="txt" value="<?php echo htmlspecialchars($bebe_data['segundo_apellido']); ?>" placeholder="Opcional">
                    </div>
                    
                    <div class="form-item">
                        <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="txt" value="<?php echo htmlspecialchars($bebe_data['fecha_nacimiento']); ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="genero">Género:</label>
                        <select id="genero" name="genero" class="txt" required>
                            <option value="" disabled selected>Seleccione su género:</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="discapacidad">Discapacidad:</label>
                        <input type="text" id="discapacidad" name="discapacidad" value="<?php echo htmlspecialchars($bebe_data['discapacidad']); ?>" placeholder="Opcional">
                    </div>
        
                    <div class="form-item">
                        <label for="alergias">Alergias:</label>
                        <input type="text" id="alergias" name="alergias" value="<?php echo htmlspecialchars($bebe_data['alergias']); ?>" placeholder="Opcional">
                    </div>
        
                    <div class="form-item">
                        <label for="enfermedades">Enfermedades:</label>
                        <input type="text" id="enfermedades" name="enfermedades" value="<?php echo htmlspecialchars($bebe_data['enfermedades']); ?>" placeholder="Opcional">
                    </div>
        
                    <div class="form-actions">
                        <a href="chat.php"><button type="button">Cancelar</button></a>
                        <button type="submit" class="register">Guardar cambios</button>
                    </div>
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
