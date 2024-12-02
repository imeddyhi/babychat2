<!DOCTYPE html>
<html>
    <head>
        <title>Cerrar Sesión</title>
    </head>
    <body>
        <?php
        session_start();
        session_unset();
        session_destroy();
        header('Location: iniciar_sesion.html'); // Redirigir a la página de inicio de sesión
        exit();
        ?>
    </body>
</html>
