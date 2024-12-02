<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Insertar registros de padres - PHP con MySQL</title>
        <style>
            body {
                font-family: Georgia;
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
                font-size: 25px;
                font-weight: bold;
                color: #000;
                position: absolute;
                top: 15px;
                right: 750px;
            }
            .next {
                font-family: Georgia, sans-serif;
                width: 100;
                background-color: #416d5a;
                color: white;
                text-decoration: none;
                padding: 10px;
                border: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 14px;
                margin-top: 10px;
                position: absolute;
                top: 50px;
                transition: background-color 0.3s;
            }
            .next:hover {
                background-color: #799674;
            }
            .preview {
                font-family: Georgia, sans-serif;
                width: 100;
                background-color: #416d5a;
                color: white;
                text-decoration: none;
                padding: 10px;
                border: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 14px;
                margin-top: 10px;
                position: absolute;
                top: 100px;
                transition: background-color 0.3s;
            }
            .preview:hover {
                background-color: #799674;
            }
        </style>
    </head>
    <body>
        <?php
            try {
                // Conexión a la base de datos
                $conMySQL = new PDO("mysql:host=localhost;dbname=babychat", "root", "");
                $conMySQL->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conMySQL->exec("SET CHARACTER SET UTF8");

                // Recolección y saneamiento de datos
                $nombres = htmlspecialchars($_POST['txtNombre']);
                $primer_apellido = htmlspecialchars($_POST['txtPrimer_Apellido']);
                $segundo_apellido = htmlspecialchars($_POST['txtSegundo_Apellido']);
                $fecha_nacimiento = htmlspecialchars($_POST['txtFecha_Nacimiento']);
                $genero = htmlspecialchars($_POST['txtGenero']);
                $email = htmlspecialchars($_POST['txtEmail']);
                $contrasena = htmlspecialchars($_POST['contrasena']);
                $confirmar = htmlspecialchars($_POST['confirmar']);

                // Verificar si las contraseñas coinciden
                if ($contrasena !== $confirmar) {
                    echo "<script>
                        alert('Las contraseñas no coinciden.');
                        window.history.back();
                    </script>";
                    exit();
                }

                // Validar que se ha seleccionado un género
                if ($genero == "Seleccione su género:") {
                    echo "<script>
                        alert('Por favor seleccione un género válido.');
                        window.history.back();
                    </script>";
                    exit();
                }

                // Preparación y ejecución de la sentencia SQL
                $sentenciaSQL = "INSERT INTO padres (email, nombres, primer_apellido, segundo_apellido, fecha_nacimiento, genero, estatus, contrasena, confirmar, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, 'A', ?, ?, NOW())";
                $sentenciaPrep = $conMySQL->prepare($sentenciaSQL);
                $sentenciaPrep->bindParam(1, $email);
                $sentenciaPrep->bindParam(2, $nombres);
                $sentenciaPrep->bindParam(3, $primer_apellido);
                $sentenciaPrep->bindParam(4, $segundo_apellido);
                $sentenciaPrep->bindParam(5, $fecha_nacimiento);
                $sentenciaPrep->bindParam(6, $genero);
                $sentenciaPrep->bindParam(7, $contrasena);
                $sentenciaPrep->bindParam(8, $confirmar);

                if ($sentenciaPrep->execute()) {
                    header('Location: bienvenida_padre.html'); // Redirigir a la página de bienvenida
                    exit();
                } else {
                    echo "<script>
                        alert('Error al almacenar el registro en la base de datos.');
                        window.history.back();
                    </script>";
                    exit();
                }

            } catch (PDOException $e) {
                echo "¡Error!: " . $e->getMessage() . "<br/>";
                die();
            } finally {
                $conMySQL = null;
            }
        ?>
    </body>
</html>
