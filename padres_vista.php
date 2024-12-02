<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "babychat";

    // Check if 'email' is set in $_GET
    if (!isset($_GET['email']) || empty($_GET['email'])) {
        throw new Exception("El parámetro 'email' no fue proporcionado en la URL.");
    }

    $email = $_GET['email'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT email, nombres, primer_apellido, segundo_apellido, fecha_nacimiento, genero, estatus, fecha_registro FROM padres WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result into an associative array
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        throw new Exception("No se encontraron resultados para el correo proporcionado.");
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
    <title>Registro - BabyChat</title>
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
            box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
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
            margin-left: 40px;
        }

        .header .imagen {
            margin-left: 80px;
            width: 150px;
            height: 150px;
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

        .form-item p {
            font-size: 16px;
            padding: 8px;
            background-color: #ffffff56;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 0;
            box-sizing: border-box;
        }

        /* Campos que ocupan el ancho completo */
        .form-item.full-width {
            grid-column: span 2;
        }

        .button-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 13px;
        }

        .editar {
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

        .editar:hover {
            background-color: #023168;
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
            <div class="header">
                <p class="title">Perfil</p>
                <img src="babyChat.png" class="imagen">
            </div>
            <div class="form-grid">
                <div class="form-item">
                    <label for="nombre">Nombres:</label>
                    <p id="txtNombre"><?php echo htmlspecialchars($row['nombres']); ?></p>
                </div>
                
                <div class="form-item">
                    <label for="primer_apellido">Primer apellido:</label>
                    <p id="txtPrimer_Apellido"><?php echo htmlspecialchars($row['primer_apellido']); ?></p>
                </div>
                <div class="form-item">
                    <label for="segundo_apellido">Segundo apellido:</label>
                    <p id="txtSegundo_Apellido"><?php echo htmlspecialchars($row['segundo_apellido']); ?></p>
                </div>
                
                <div class="form-item">
                    <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                    <p id="txtFecha_Nacimiento"><?php echo htmlspecialchars($row['fecha_nacimiento']); ?></p>
                </div>
                <div class="form-item">
                    <label for="genero">Género:</label>
                    <p id="txtGenero"><?php echo htmlspecialchars($row['genero']); ?></p>
                </div>
                <div class="form-item">
                        <label for="fecha_registro">Fecha de registro:</label>
                        <p id="txtFecha_Registro"><?php echo htmlspecialchars($row['fecha_registro']); ?></p>
                    </div>

                <div class="form-item full-width">
                    <label for="correo">Correo:</label>
                    <p id="txtCorreo"><?php echo htmlspecialchars($row['email']); ?></p>
                </div>
            </div>

            <div class="button-container">
                <a href="padres_editar.php?email=<?php echo urlencode($email); ?>"  class="editar">Editar</a>
                <a href="lista_padres.php?email=<?php echo urlencode($email); ?>" class="volver">Volver</a>
            </div>
        </div>
    </main>
</body>
</html>