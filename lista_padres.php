<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "babychat";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT email, CONCAT(nombres, ' ', primer_apellido) AS nombre_completo, estatus FROM padres");
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administración de Usuarios - BabyChat</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                margin: 0;
                font-family: sans-serif;
                background-color: #F4F5F7;
            }

            main {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            .admin-container {
                width: 60%;
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .header {
                display: flex;
                align-items: center;
                justify-content: space-between; /* Changed from center to space-between */
                margin-bottom: 20px;
            }

            .header .title {
                font-size: 30px;
                text-align: center;
                margin-left: 0;
                flex-grow: 1; /* Allows the title to take up available space */
            }

            .header .imagen {
                width: 150px;
                height: 150px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 12px;
                text-align: center;
            }

            th {
                background-color: #c5cedb;
                color: #000000;
            }

            .button-container {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                width: 100%;
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
            button.edit {
                background-color: #07a1b6;
            }
            button.edit:hover {
                background-color: #058393;
            }
            button.status {
                background-color: #74761f;
            }
            button.status:hover {
                background-color: #4e4f13;
            }
            button.delete {
                background-color: #ce0000;
            }
            button.delete:hover {
                background-color: #a00202;
            }

            button:hover {
                background-color: #023168;
                transform: scale(0.95);
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>
    <body>
        <main>
            <div class="admin-container">
                <div class="header">
                    <p class="title">Usuarios BabyChat</p>
                    <img src="babyChat.png" class="imagen" alt="Logo BabyChat">
                    <a href="bienvenida_admin.php" class="button back-button"><button>Inicio ⌂</button></a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>email</th>
                            <th>Usuario</th>
                            <th>Estatus</th> <!-- Nueva columna de estatus -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td><i class='fas fa-user' style='margin-right: 10px; font-size: 20px;'></i> " . $row["nombre_completo"] . "</td>";
                                echo "<td>" . $row["estatus"] . "</td>"; // Mostrar el estatus
                                echo "<td>
                                        <div class='button-container'>
                                            <div class='ver'>
                                                <a class='verr' href='padres_vista.php?email=" . $row['email'] . "'><button>Ver</button></a>
                                            </div>
                                            <div class='editar'>
                                                <a class='editarr' href='padres_editar.php?email=" . $row['email'] . "'><button class='edit'>Editar</button></a>
                                            </div>
                                            <div class='nuevo'>
                                                <a class='nuevoo' href='padre_estatus.php?email=" . $row['email'] . "'><button class='status'>Estatus</button></a>
                                            </div>
                                            <div class='delete'>
                                                <a class='deletee' href='padres_eliminar.php?email=" . $row['email'] . "'><button class='delete'>Eliminar</button></a>
                                            </div>
                                        </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No hay resultados</td></tr>"; // Cambiado a 3 columnas
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </body>
    </html>
    <?php

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>
