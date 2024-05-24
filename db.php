<?php
//$dbHost = '65.99.248.156';
//$dbUsuario = 'wecodesp_crmpavel';
//$dbPassword = 'p+YZZ*V^&u(m';
//$dbName = 'wecodesp_crm';

$dbHost = 'localhost';
$dbUsuario = 'root';
$dbPassword = '';
$dbName = 'cerreme';

$conn = mysqli_connect($dbHost, $dbUsuario, $dbPassword, $dbName);

if (mysqli_connect_errno()) {
    // Ocurrió un error al intentar conectar a la base de datos
    echo "Error al conectar a la base de datos: " . mysqli_connect_error();
    // Puedes agregar más acciones en caso de error, como mostrar un mensaje de error o detener la ejecución del programa.
    exit();
}
else{
}

// Establecer la zona horaria a México, Ciudad de México (CDMX)
date_default_timezone_set('America/Mexico_City');
