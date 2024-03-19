<?php
// $dbHost = '212.1.209.129';
// $dbUsuario = 'u901451733_gcrm';
// $dbPassword = 'Hola.mundo!@94*';
// $dbName = 'u901451733_gcrm';

$dbHost = 'localhost';
$dbUsuario = 'root';
$dbPassword = '';
$dbName = 'crm';

$conn = mysqli_connect($dbHost, $dbUsuario, $dbPassword, $dbName);

if (mysqli_connect_errno()) {
    // Ocurrió un error al intentar conectar a la base de datos
    echo "Error al conectar a la base de datos: " . mysqli_connect_error();
    // Puedes agregar más acciones en caso de error, como mostrar un mensaje de error o detener la ejecución del programa.
    exit();
}

// Establecer la zona horaria a México, Ciudad de México (CDMX)
date_default_timezone_set('America/Mexico_City');
