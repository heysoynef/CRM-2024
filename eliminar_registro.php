<?php
include 'db.php'; // Incluir la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tabla = $_POST['tabla'];
    $id = $_POST['id'];

    // Consulta SQL para eliminar el registro
    $sql = "DELETE FROM $tabla WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: data.php?id=" . urlencode($tabla)); // Redirigir a la página anterior después de eliminar el registro
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    echo "Solicitud no válida.";
}
