<?php
    session_start();
    if ($_SESSION['role'] !== 'padre') {
        header("Location: iniciar_sesion.html");
        exit();
    }

    require 'conexion.php';

    $email_padre = $_SESSION['email']; // Obtiene el email del padre que ha iniciado sesión

    // Verificar si se ha enviado un ID de bebé a través del formulario de búsqueda o como parámetro GET
    $id_bebe = null;
    if (isset($_POST['id_bebe']) && !empty($_POST['id_bebe'])) {
        $id_bebe = intval($_POST['id_bebe']);
    } elseif (isset($_GET['id_bebe'])) {
        $id_bebe = intval($_GET['id_bebe']);
    }

    // Consulta para obtener el historial de estatura del bebé si hay un ID válido
    if ($id_bebe) {
        $query_estatura = "SELECT id_estatura, estatura, fecha, id_bebe FROM seguimiento_estatura WHERE id_bebe = ?";
        $stmt_estatura = $conn->prepare($query_estatura);
        $stmt_estatura->bind_param('i', $id_bebe);
        $stmt_estatura->execute();
        $result_estatura = $stmt_estatura->get_result();
    }

    // Consulta para obtener los bebés que pertenecen al padre que ha iniciado sesión
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
    <title>Historial de Estatura del Bebé</title>
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
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
            width: 900px;
            max-width: 90%;
        }

        .header-title {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        .selector {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .selector label {
            font-size: 18px;
            margin-right: 10px;
        }

        .selector select {
            flex-grow: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background-color: var(--input-bg-color);
            color: var(--input-text-color);
            transition: background-color 0.3s, color 0.3s;
            font-size: 16px;
        }

        .selector select:focus {
            outline: none;
            border-color: #0a84ff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--table-bg-color);
            color: var(--table-text-color);
        }

        td {
            background-color: var(--table-bg-color);
            color: var(--table-text-color);
        }

        a {
            color: white;
            text-decoration: none;
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
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            margin-top: 10px;
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

        button:hover {
            background-color: #025068;
            transform: scale(0.95);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        body.dark-mode {
            --box-bg-color: #000000;
            --table-bg-color: #000000;
            --table-text-color: #ffffff;
        }

        body.light-mode {
            --box-bg-color: #ffffff;
            --table-bg-color: #f0f0f5;
            --table-text-color: #000000;
        }
    </style>
</head>
<body>
    <main>
        <div class="box">
            <div class="header">
                <h2 class="header-title">Historial de Estatura del Bebé</h2>
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
                <a href="#" onclick="agregarEstatura()"><button>+ Agregar</button></a>
            </div>

            <?php if ($id_bebe && isset($result_estatura) && $result_estatura->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Estatura</th>
                            <th>Fecha</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_estatura->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['estatura']); ?>cm</td>
                                <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                <td>
                                    <a href="estatura_editar.php?id_estatura=<?php echo htmlspecialchars($row['id_estatura']); ?>&id_bebe=<?php echo htmlspecialchars($row['id_bebe']); ?>"><button class="edit">Editar</button></a>
                                </td>
                                <td>
                                    <a href="estatura_eliminar.php?id_estatura=<?php echo htmlspecialchars($row['id_estatura']); ?>&id_bebe=<?php echo htmlspecialchars($row['id_bebe']); ?>"><button class="delete">Eliminar</button></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif ($id_bebe): ?>
                <p>No se encontraron registros de estatura para este bebé.</p>
            <?php endif; ?>

            <?php
                if (isset($stmt_estatura)) {
                    $stmt_estatura->close();
                }
            ?>
            <a href="chat.php"><button>Regresar a Chat</button></a>
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

        function agregarEstatura() {
            const idBebe = document.getElementById('bebes').value;
            if (idBebe) {
                window.location.href = `estatura_agregar.php?id_bebe=${idBebe}`;
            } else {
                alert('Por favor, selecciona un bebé primero.');
            }
        }
    </script>
</body>
</html>
