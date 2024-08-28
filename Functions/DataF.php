<?php

function verificarSesion()
{
    if (!isset($_SESSION["id"])) {
        header("Location: index.php");
        exit();
    }
}

function verificarTipoCliente()
{
    if ($_SESSION["type"] === "Cliente") {
        $tabla = isset($_GET['id']) ? $_GET['id'] : '';
        $nombre_campo_cliente = obtenerNombreCampoCliente($_SESSION["id"]);
        if ($nombre_campo_cliente != $tabla) {
            header("Location: data.php?id=" . urlencode($nombre_campo_cliente));
            exit();
        }
    }
}

function obtenerNombreCampoCliente($id_usuario)
{
    include 'db.php'; // Incluir la conexión a la base de datos

    $sql = "SELECT cliente FROM users WHERE id = $id_usuario";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["cliente"];
    } else {
        return "";
    }
}

function construirConsulta($tabla, $mesFiltro, $anioFiltro)
{
    $query = "SELECT * FROM $tabla";
    if (!empty($mesFiltro) || !empty($anioFiltro)) {
        $query .= " WHERE";
        if (!empty($mesFiltro)) {
            $query .= " MONTH(fecha) = '$mesFiltro'";
            if (!empty($anioFiltro)) {
                $query .= " AND";
            }
        }
        if (!empty($anioFiltro)) {
            $query .= " YEAR(fecha) = '$anioFiltro'";
        }
        $query .= " ORDER BY id DESC";
    } else {
        $mesActual = date('m');
        $anioActual = date('Y');
        $query .= " WHERE MONTH(fecha) = '$mesActual' AND YEAR(fecha) = '$anioActual' ORDER BY id DESC";
    }

    return $query;
}

function obtenerTotalRegistros($query)
{
    include 'db.php'; // Incluir la conexión a la base de datos
    return mysqli_query($conn, $query);
}

function obtenerDatos($query)
{
    include 'db.php'; // Incluir la conexión a la base de datos
    return mysqli_query($conn, $query);
}

?>
