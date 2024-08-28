
<?php
//AQUI ESTA TODA LA PARTE DE LAS GRAFICAS DE CHART.PHP
// Incluir la conexión a la base de datos
require 'db.php'; 

// Función para redirigir si el usuario no ha iniciado sesión o no es cliente
function verificarSesion() {
    if (!isset($_SESSION["id"])) {
        header("Location: index.php");
        exit();
    }

    if ($_SESSION["type"] === "Cliente") {
        $nombre_campo_cliente = obtenerNombreCampoCliente($_SESSION["id"]);
        header("Location: chart_client.php?tabla=" . urlencode($nombre_campo_cliente));
        exit();
    }
}

// Función para obtener el nombre del campo de cliente del usuario
function obtenerNombreCampoCliente($id_usuario) {
    global $conn;
    $sql = "SELECT cliente FROM users WHERE id = $id_usuario";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["cliente"];
    } else {
        return ""; // Retorna una cadena vacía si no se encuentra el campo
    }
}

// Función para obtener los nombres de las tablas, excluyendo 'users'
function obtenerTablas() {
    global $conn;
    $tablas = array();
    $sql = "SHOW TABLES";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_row()) {
            $tabla = $row[0];
            if ($tabla != 'users') {
                $tablas[] = $tabla;
            }
        }
    }

    return $tablas;
}

// Función para obtener la cantidad de registros por mes y año en una tabla
function obtenerLeadsPorMesYAnio($tabla, $mes, $anio) {
    global $conn;
    $sql = "SELECT COUNT(*) AS total FROM $tabla WHERE MONTH(fecha) = $mes AND YEAR(fecha) = $anio";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total"];
    } else {
        return 0;
    }
}
?>
