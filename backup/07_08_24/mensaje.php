<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Obtener el ID del mensaje enviado por AJAX
$messageId = $_POST['id'];

// Incluir el archivo de conexión a la base de datos
require_once 'db.php';

// Realizar una consulta a la base de datos para obtener los detalles del mensaje con el ID
$query = "SELECT * FROM frikitravel WHERE id = $messageId";
$result = mysqli_query($db, $query);

if ($result) {
    // Verificar si se encontraron resultados
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Obtener los detalles del mensaje
        $messageContent = $row['contenido'];
        // ...

        // Generar el contenido HTML de los detalles del mensaje
        $messageDetails = '<h4>Detalles del mensaje</h4>';
        $messageDetails .= '<p>Contenido: ' . $messageContent . '</p>';
        // ...

        // Devolver los detalles del mensaje en formato HTML
        echo $messageDetails;
    } else {
        echo 'No se encontraron detalles del mensaje.';
    }
} else {
    echo 'Error al obtener los detalles del mensaje: ' . mysqli_error($db);
}

// Cerrar la conexión a la base de datos
mysqli_close($db);
