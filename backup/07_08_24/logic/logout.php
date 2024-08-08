<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: ../index.php");
    exit();
}

// Cerrar sesión al hacer clic en el enlace "Cerrar Sesión"
if (isset($_GET["logout"])) {
    session_destroy(); // Destruir todas las variables de sesión
    header("Location: ../index.php");
    exit();
}
