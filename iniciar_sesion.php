<?php
session_start();
require 'conexion.php'; // Asegúrate de que este archivo tenga la configuración de tu conexión a la base de datos.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    // Consulta para verificar si el usuario es un administrador
    $admin_query = "SELECT estatus FROM admin WHERE email = ? AND contrasena = ?";
    $stmt = $conn->prepare($admin_query);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    // Verificar si se encontró un registro
    if ($admin_result->num_rows > 0) {
        $row = $admin_result->fetch_assoc();
        $estatus = $row['estatus'];
        
        // Verificar el estatus de la cuenta
        if ($estatus == 'A') {
            // Estatus activo, permitir el inicio de sesión
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'admin';
            header("Location: bienvenida_admin.php");
            exit();
        } elseif ($estatus == 'I') {
            // Estatus inactivo, cuenta suspendida por el administrador
            echo "<script>alert('La cuenta fue suspendida.'); window.location.href='iniciar_sesion.html';</script>";
        } elseif ($estatus == 'E') {
            // Estatus eliminado, cuenta eliminada por el usuario
            echo "<script>alert('La cuenta fue eliminada.'); window.location.href='iniciar_sesion.html';</script>";
        }
    } else {
        // Si no encuentra al usuario, mostrar un mensaje de error
        echo "<script>alert('Correo o contraseña incorrectos.'); window.location.href='iniciar_sesion.html';</script>";
    }


    // Consulta para verificar si el usuario es un padre
    $parent_query = "SELECT estatus FROM padres WHERE email = ? AND contrasena = ? AND confirmar = ?";
    $stmt = $conn->prepare($parent_query);
    $stmt->bind_param('sss', $email, $password, $password); // Confirmar la contraseña
    $stmt->execute();
    $parent_result = $stmt->get_result();

    // Verificar si se encontró un registro
    if ($parent_result->num_rows > 0) {
        $row = $parent_result->fetch_assoc();
        $estatus = $row['estatus'];
        
        // Verificar el estatus de la cuenta
        if ($estatus == 'A') {
            // Estatus activo, permitir el inicio de sesión
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'padre';
            header("Location: chat.php");
            exit();
        } elseif ($estatus == 'I') {
            // Estatus inactivo, cuenta suspendida por el administrador
            echo "<script>alert('La cuenta fue suspendida por un administrador.'); window.location.href='iniciar_sesion.html';</script>";
        } elseif ($estatus == 'E') {
            // Estatus eliminado, cuenta eliminada por el usuario
            echo "<script>alert('La cuenta fue eliminada por el usuario.'); window.location.href='iniciar_sesion.html';</script>";
        }
    } else {
        // Si no encuentra al usuario, mostrar un mensaje de error
        echo "<script>alert('Correo o contraseña incorrectos.'); window.location.href='iniciar_sesion.html';</script>";
    }

    }
?>
