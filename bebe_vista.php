<?php
    session_start();
    if ($_SESSION['role'] !== 'padre') {
        header("Location: iniciar_sesion.html");
        exit();
    }

    require 'conexion.php';

    // Obtener el email del usuario (padre) que ha iniciado sesión
    $email_padre = $_SESSION['email'];

    $id_bebe = null;
    if (isset($_POST['id_bebe']) && !empty($_POST['id_bebe'])) {
        $id_bebe = intval($_POST['id_bebe']);
    } elseif (isset($_GET['id_bebe'])) {
        $id_bebe = intval($_GET['id_bebe']);
    }

    $bebes = [];
    if ($id_bebe) {
        $query_bebe = "SELECT nombres, primer_apellido, segundo_apellido, fecha_nacimiento, genero, discapacidad, alergias, enfermedades FROM bebes WHERE id_bebe = ? AND email = ?";
        $stmt_bebe = $conn->prepare($query_bebe);
        $stmt_bebe->bind_param('is', $id_bebe, $email_padre);
        $stmt_bebe->execute();
        $result_bebe = $stmt_bebe->get_result();
        $bebes = $result_bebe->fetch_assoc();
    }

    // Modificar la consulta para filtrar los bebés por el email del padre
    $query_bebes = "SELECT id_bebe, nombres FROM bebes WHERE email = ?";
    $stmt_bebes = $conn->prepare($query_bebes);
    $stmt_bebes->bind_param('s', $email_padre);
    $stmt_bebes->execute();
    $result_bebes = $stmt_bebes->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Bebé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
            background-color: #f5f5f5;
            color: #000;
        }

        body.dark-mode {
            background-color: #1c1c1e;
            color: #ffffff;
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
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 24px;
            text-align: center;
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

        .info {
            display: flex;
            flex-direction: column;
            display: grid;
            grid-template-columns: 1fr 1fr; /* Dos columnas de igual ancho */
            gap: 15px; /* Espacio entre columnas */
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item label {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5px;
        }
        .info-item span {
            width: 100%;
            padding: 8px;
            color: var(--input-text-color);
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: var(--input-bg-color);
        }

        button {
            padding: 10px 18px;
            border-radius: 20px;
            border: none;
            background-color: #0a84ff;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #025068;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        button.edit {
            background-color: #07a1b6;
        }
        button.delete {
            background-color: #ce0000;
        }
        button.delete:hover {
            background-color: #a00202;
        }

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
                <h2 class="header-title">Perfil del Bebé</h2>
                <div class="profile-header">
                    <img src="babyChat.png" alt="Imagen de BabyChat">
                </div>
            </div>
            <form class="selector" method="post" action="" id="bebesForm">
                <label for="bebes">Selecciona a tu bebé:</label>
                <select id="bebes" name="id_bebe" onchange="document.getElementById('bebesForm').submit();">
                    <?php
                    while ($row = $result_bebes->fetch_assoc()) {
                        $selected = ($id_bebe == $row['id_bebe']) ? 'selected' : '';
                        echo "<option value='{$row['id_bebe']}' {$selected}>{$row['nombres']}</option>";
                    }
                    ?>
                </select>
            </form>
            <?php if ($bebes): ?>
                <div class="info">
                    <div class="info-item">
                        <label>Nombre:</label>
                        <span><?php echo htmlspecialchars($bebes['nombres']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Primer Apellido:</label>
                        <span><?php echo htmlspecialchars($bebes['primer_apellido']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Segundo Apellido:</label>
                        <span><?php echo htmlspecialchars($bebes['segundo_apellido']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Fecha de Nacimiento:</label>
                        <span><?php echo htmlspecialchars($bebes['fecha_nacimiento']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Género:</label>
                        <span><?php echo htmlspecialchars($bebes['genero']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Discapacidad:</label>
                        <span><?php echo htmlspecialchars($bebes['discapacidad']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Alergias:</label>
                        <span><?php echo htmlspecialchars($bebes['alergias']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Enfermedades:</label>
                        <span><?php echo htmlspecialchars($bebes['enfermedades']); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>No se ha seleccionado ningún bebé o no hay datos disponibles.</p>
            <?php endif; ?>
            <a href="chat.php"><button>Volver a Chat</button></a>
            <a href="bebe_agregar.php"><button>Agregar nuevo bebé</button></a>
            <a href="bebe_editar.php?id_bebe=<?php echo urlencode($id_bebe); ?>"><button class="edit">Editar</button></a>
            <a href="bebe_eliminar.php?id_bebe=<?php echo urlencode($id_bebe); ?>"><button class="delete">Eliminar</button></a>
        </div>
    </main>
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

        applyTheme();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    </script>
</body>
</html>
