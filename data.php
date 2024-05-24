<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Verificar si el usuario tiene el tipo "cliente"
if ($_SESSION["type"] === "Cliente") {
    //obtener el nombre de la tabla mediante GET
    $tabla = isset($_GET['id']) ? $_GET['id'] : '';
    // Obtener el nombre del campo de cliente del usuario
    $nombre_campo_cliente = obtenerNombreCampoCliente($_SESSION["id"]); // Ajusta esto según tu sistema
    //verificar si el cliente es el mismo de la url para proteccion de datos
    if ($nombre_campo_cliente != $tabla) {
        // Redirigir al usuario a chart.php con los parámetros adecuados
        header("Location: data.php?id=" . urlencode($nombre_campo_cliente));
        exit();
    }
}

function obtenerNombreCampoCliente($id_usuario)
{
    include 'db.php'; // Incluir la conexión a la base de datos

    // Consulta SQL para obtener el nombre del campo de cliente del usuario
    $sql = "SELECT cliente FROM users WHERE id = $id_usuario"; // Ajusta esto según tu estructura de base de datos y nombres de tablas y campos

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["cliente"];
    } else {
        return ""; // Si no se encuentra el campo, retorna una cadena vacía
    }
}

include 'layouts/header.php';
include 'db.php';

// Obtener el ID de la imagen enviado por la URL
$tabla = $_GET["id"];

$mesFiltro = isset($_GET["mes"]) ? $_GET["mes"] : "";
$query = "SELECT * FROM $tabla";
if (!empty($mesFiltro)) {
    $query .= " WHERE MONTH(fecha) = '$mesFiltro' ORDER BY id DESC";
} else {
    $mesActual = date('m');  // Obtener el número del mes actual
    $query .= " WHERE MONTH(fecha) = '$mesActual' ORDER BY id DESC";
}

// Establecer la cantidad de registros por página
$registrosPorPagina = 30;

// Obtener el número de página actual
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Calcular el desplazamiento para la consulta SQL
$desplazamiento = ($paginaActual - 1) * $registrosPorPagina;

// ANTES - Obtener el número total de registros
// $resultTotal = mysqli_query($conn, $query);
// $totalRegistros = mysqli_num_rows($resultTotal);

// AHORA - Obtener el número total de registros
$resultTotal = mysqli_query($conn, $query);
if ($resultTotal !== false) {
    $totalRegistros = mysqli_num_rows($resultTotal);

    // Calcular el número total de páginas
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
} else {
    echo 'Error en la consulta: ' . mysqli_error($conn);
    exit(); // Salir del script si hay un error en la consulta
}

// Calcular el número total de páginas
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Modificar la consulta SQL para incluir la paginación
$query .= " LIMIT $desplazamiento, $registrosPorPagina";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <div class="row justify-content-end">
        <div class="col-md-4">
            <form method="POST" action="carga.php" enctype="multipart/form-data" class="form-group">
                <input type="file" id="csvFile" name="csvFile" accept=".csv" required style="display: none;">
                <input type="hidden" name="clienteId" value="<?php echo $_GET['id']; ?>">
                <button type="button" class="btn btn-primary"
                    onclick="document.getElementById('csvFile').click();">Subir Archivo</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" name="submit" style="display: none;">Subir
                    Archivo CSV</button>
            </form>
        </div>
    </div>

    <div class="row">

        <div class="col-md-8">
            <h3 class="mb-3"><?php echo $_GET["id"] ?></h3>
        </div>

        <div class="col-md-4">
            <select name="mes" id="mes" class="form-control my-2">
                <option disabled>Selecciona un mes</option>
                <?php
                $mesActual = date('m');  // Obtener el número del mes actual
                $mesSeleccionado = $_GET['mes'] ?? $mesActual;  // Obtener el valor del parámetro 'mes' de la URL, o el mes actual si no está presente
                
                $nombresMeses = array(
                    '01' => 'Enero',
                    '02' => 'Febrero',
                    '03' => 'Marzo',
                    '04' => 'Abril',
                    '05' => 'Mayo',
                    '06' => 'Junio',
                    '07' => 'Julio',
                    '08' => 'Agosto',
                    '09' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre'
                );

                foreach ($nombresMeses as $mesNumero => $nombreMes) {
                    $mesSeleccionadoHTML = ($mesSeleccionado == $mesNumero) ? 'selected' : '';
                    printf('<option value="%s" %s>%s</option>', $mesNumero, $mesSeleccionadoHTML, $nombreMes);
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col">
            <?php
            // Imprimir la tabla con los datos obtenidos
            if ($result !== false) {
                if (mysqli_num_rows($result) > 0) {
                    ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <?php
                                    // Imprimir los encabezados (th) utilizando las columnas de la tabla
                                    $row = mysqli_fetch_assoc($result);
                                    foreach ($row as $column => $value) {
                                        echo '<th class="table-row" scope="col">' . $column . '</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody style="font-size:13px;">
                                <?php
                                // Imprimir los datos (filas) utilizando un bucle foreach
                                mysqli_data_seek($result, 0); // Reiniciar el puntero del resultado
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <?php
                                        foreach ($row as $column => $value) {
                                            if ($column == 'fecha') {
                                                // Convertir la fecha al formato deseado (día mes año)
                                                $formattedDate = date('d M Y', strtotime($value));
                                                echo '<td>' . $formattedDate . '</td>';
                                            } elseif ($column == 'mensaje') {
                                                // Limitar el campo "mensaje" a 30 caracteres
                                                if ($value !== null) {
                                                    // Solo calcular la longitud si $value no es null
                                                    $truncatedValue = (strlen($value) > 30) ? substr($value, 0, 30) . '...' : $value;
                                                } else {
                                                    // Manejo en caso de que $value sea null
                                                    $truncatedValue = 'No data'; // o cualquier otro valor predeterminado
                                                }
                                                ?>
                                                <td data-toggle="modal" data-target="#mensajeModal<?php echo $row['id']; ?>"
                                                    style="cursor: pointer;"><?php echo $truncatedValue; ?></td>
                                                <div id="mensajeModal<?php echo $row['id']; ?>" class="modal fade" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">Mensaje</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" style="font-size:14px; text-align: justify;">
                                                                <?php echo $value; ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                echo '<td>' . $value . '</td>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination my-4">
                        <?php
                        // Mostrar los enlaces de paginación solo si hay más de una página
                        if ($totalPaginas > 1) {
                            if ($paginaActual > 1) {
                                echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&pagina=' . ($paginaActual - 1) . '" class="pagination-link mx-3">Anterior</a>';
                            }
                            for ($i = 1; $i <= $totalPaginas; $i++) {
                                if ($i == $paginaActual) {
                                    echo '<span class="pagination-link current">' . $i . '</span>';
                                } else {
                                    echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&pagina=' . $i . '" class="pagination-link mx-3">' . $i . '</a>';
                                }
                            }
                            if ($paginaActual < $totalPaginas) {
                                echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&pagina=' . ($paginaActual + 1) . '" class="pagination-link mx-3">Siguiente</a>';
                            }
                        }
                        ?>
                    </div>
                    <?php
                    // Agregar enlace para descargar el archivo Excel
                    echo '<div class="text-center mt-4">';
                    // Se genera un enlace con la etiqueta <a> que apunta al archivo "exportar.php" con dos parámetros, "id" y "mes", cuyos valores se obtienen de las variables $id y $mesFiltro respectivamente. El enlace tiene una clase CSS "btn btn-primary" que le da el estilo de un botón primario.
                    $mes = empty($mesFiltro) ? $mesActual : $mesFiltro;
                    echo '<a href="logic/export.php?id=' . $tabla . '&mes=' . $mes . '" class="btn btn-primary">Exportar a Excel</a>';
                    echo '</div>'; // Se cierra el contenedor div.
            
                } else {
                    echo 'No se encontraron resultados.';
                }
            } else {
                echo 'Error en la consulta: ' . mysqli_error($conn);
            }

            // Cerrar la conexión a la base de datos
            mysqli_close($conn);
            ?>
        </div>
    </div>
</div>

<script>
    // Escucha el evento de cambio en el elemento con el id "mes"
    document.getElementById("mes").addEventListener("change", function () {
        // Obtiene el valor actual del campo de entrada o selección
        var mes = this.value;

        // Obtiene la URL base sin parámetros de consulta
        var url = window.location.href.split('?')[0];

        // Actualiza la URL agregando los parámetros de consulta "id" y "mes" con los valores correspondientes
        window.location.href = url + '?id=<?php echo $_GET["id"]; ?>&mes=' + mes;
    });
</script>
<script>
    document.getElementById('csvFile').addEventListener('change', function () {
        document.getElementById('submitBtn').click();
    });
</script>



<?php include 'layouts/footer.php'; ?>