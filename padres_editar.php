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
            $mensaje_exito = "Guardado con éxito";
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
            margin-right: 30px;
        }

        .header .imagen {
            margin-left: 30px;
            width: 150px;
            height: 150px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #ffffff56;
        }

        .form-item.full-width {
            grid-column: span 2;
        }

        .button-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 13px;
            width: 100%;
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

        .success-message {
            margin-top: 10px;
            font-size: 14px;
            color: green;
            margin-left: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <main>
        <div class="Canva1">
            <div class="header">
                <p class="title">Editar Perfil</p>
                <img src="babyChat.png" class="imagen">
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
                        <input type="text" id="genero" name="genero" value="<?php echo htmlspecialchars($row['genero']); ?>" required>
                        <?php if (!empty($mensaje_exito)): ?>
                            <span class="success-message"><?php echo $mensaje_exito; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="button-container">
                    <button type="submit">Guardar Cambios</button>
                    <a href="lista_padres.php?email=<?php echo urlencode($email); ?>" class="volver">Volver</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
