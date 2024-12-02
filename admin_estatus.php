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

    $stmt = $conn->prepare("SELECT email, nombres, estatus FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        throw new Exception("No se encontraron resultados para el correo proporcionado.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $estatus = $_POST['estatus'];

        $updateStmt = $conn->prepare("UPDATE admin SET estatus = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $estatus, $email);

        if ($updateStmt->execute()) {
            $mensaje_exito = "Estatus actualizado con éxito";
            $row['estatus'] = $estatus;
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
    <title>Cambiar Estatus - BabyChat</title>
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
            width: 400px;
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
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-item label {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .form-item select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #ffffff56;
        }

        .button-container {
            margin-top: 20px;
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
                <p class="title">Cambiar Estatus</p>
            </div>
            <form method="POST" action="">
                <div class="form-item">
                    <label for="estatus">Estatus:</label>
                    <select id="estatus" name="estatus" required>
                        <option value="A" <?php echo ($row['estatus'] == 'A') ? 'selected' : ''; ?>>Activo</option>
                        <option value="I" <?php echo ($row['estatus'] == 'I') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="button-container">
                    <button type="submit">Guardar Cambios</button>
                    <a href="lista_admin.php?email=<?php echo urlencode($email); ?>" class="volver">Volver</a>
                </div>
            </form>
            <?php if (!empty($mensaje_exito)): ?>
                <span class="success-message"><?php echo $mensaje_exito; ?></span>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
