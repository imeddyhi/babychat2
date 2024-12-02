<?php
session_start();
if ($_SESSION['role'] !== 'padre') {
    header("Location: iniciar_sesion.html");
    exit();
}

require 'conexion.php';

$email_padre = $_SESSION['email']; // Obtiene el email del padre que ha iniciado sesión

$id_bebe = null;
if (isset($_POST['id_bebe']) && !empty($_POST['id_bebe'])) {
    $id_bebe = intval($_POST['id_bebe']);
} elseif (isset($_GET['id_bebe'])) {
    $id_bebe = intval($_GET['id_bebe']);
}

if ($id_bebe) {
    $query_vacunacion = "SELECT * FROM seguimiento_vacunacion WHERE id_bebe = ?";
    $stmt_vacunacion = $conn->prepare($query_vacunacion);
    $stmt_vacunacion->bind_param('i', $id_bebe);
    $stmt_vacunacion->execute();
    $result_vacunacion = $stmt_vacunacion->get_result();
}

// Modifica la consulta para obtener solo los bebés que pertenecen al padre que ha iniciado sesión
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
    <title>Historial de Vacunación</title>
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
            width: 1000px;
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
                <h2 class="header-title">Historial de Vacunación del Bebé</h2>
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
                <a href="#" onclick="agregarVacuna()"><button>+ Agregar</button></a>
            </div>

            <?php if ($id_bebe && isset($result_vacunacion) && $result_vacunacion->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vacuna</th>
                            <th>Enfermedad que Previene</th>
                            <th>Dosis</th>
                            <th>Edad y Frecuencia</th>
                            <th>Fecha de Aplicación</th>
                            <th>Lote</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_vacunacion->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['vacuna']); ?></td>
                                <td><?php echo htmlspecialchars($row['enfermedad_previene']); ?></td>
                                <td><?php echo htmlspecialchars($row['dosis']); ?></td>
                                <td><?php echo htmlspecialchars($row['edad_frecuencia']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha_aplicacion']); ?></td>
                                <td><?php echo htmlspecialchars($row['lote']); ?></td>
                                <td>
                                    <a href="vacuna_editar.php?id_vacuna=<?php echo htmlspecialchars($row['id_vacuna']); ?>&id_bebe=<?php echo htmlspecialchars($row['id_bebe']); ?>"><button class="edit">Editar</button></a>
                                </td>
                                <td>
                                    <a href="vacuna_eliminar.php?id_vacuna=<?php echo htmlspecialchars($row['id_vacuna']); ?>&id_bebe=<?php echo htmlspecialchars($row['id_bebe']); ?>"><button class="delete">Eliminar</button></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif ($id_bebe): ?>
                <p>No se encontraron registros de vacunación para este bebé.</p>
            <?php endif; ?>

            <?php
                if (isset($stmt_vacunacion)) {
                    $stmt_vacunacion->close();
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

        function agregarVacuna() {
            const idBebe = document.getElementById('bebes').value;
            window.location.href = `vacuna_agregar.php?id_bebe=${idBebe}`;
        }
    </script>
</body>
</html>
