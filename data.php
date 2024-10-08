<?php
session_start(); // Iniciar sesión

// Incluir la funcionalidad
require 'Functions/DataF.php';
require 'db.php';

// Verificar si el paciente no ha iniciado sesión
verificarSesion();

// Verificar si el usuario tiene el tipo "cliente"
verificarTipoCliente();

include 'layouts/header.php';

$tabla = $_GET["id"];
$mesFiltro = isset($_GET["mes"]) ? $_GET["mes"] : "";
$anioFiltro = isset($_GET["anio"]) ? $_GET["anio"] : "";

$query = construirConsulta($tabla, $mesFiltro, $anioFiltro);

$registrosPorPagina = 30;
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$desplazamiento = ($paginaActual - 1) * $registrosPorPagina;

$resultTotal = obtenerTotalRegistros($query);
if ($resultTotal !== false) {
    $totalRegistros = mysqli_num_rows($resultTotal);
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
} else {
    echo 'Error en la consulta: ' . mysqli_error($conn);
    exit();
}

$query .= " LIMIT $desplazamiento, $registrosPorPagina";
$result = obtenerDatos($query);
?>




<div class="container">
    <div class="row justify-content-end">
        <?php

        if ($_SESSION["type"] === "Super_Admin") {
        ?>
            <div class="col-md-4">
                <a class="btn btn-success" href="Loadcsv.php/?tabla=<?php echo htmlspecialchars($_GET['id']); ?>">Carga CSV</a>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="row">
        <div class="col-md-4">
            <h3 class="mb-3"><?php echo $_GET["id"] ?></h3>
        </div>
        <div class="col-md-4">

        </div>
        <div class="col-md-2">
            <select name="mes" id="mes" class="form-control my-2" style="font-size: 14px;">
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

        <div class="col-md-2">
            <select name="anio" id="anio" class="form-control my-2" style="font-size: 14px;">
                <option disabled>Selecciona un año</option>
                <?php
                $anioActual = date('Y');  // Obtener el año actual
                $anioSeleccionado = $_GET['anio'] ?? $anioActual;  // Obtener el valor del parámetro 'anio' de la URL, o el año actual si no está presente

                for ($i = 2020; $i <= $anioActual; $i++) { // Puedes ajustar el rango de años según tus necesidades
                    $anioSeleccionadoHTML = ($anioSeleccionado == $i) ? 'selected' : '';
                    printf('<option value="%s" %s>%s</option>', $i, $anioSeleccionadoHTML, $i);
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
                                    if ($_SESSION["type"] === "Super_Admin") {
                                        echo '<th scope="col">borrar</th>'; // Añadir una columna para acciones
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
                                                <td data-toggle="modal" data-target="#mensajeModal<?php echo $row['id']; ?>" style="cursor: pointer;"><?php echo $truncatedValue; ?></td>
                                                <div id="mensajeModal<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
                                        <?php

                                        if ($_SESSION["type"] === "Super_Admin") {
                                        ?>
                                            <td>
                                                <form action="eliminar_registro.php" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este registro?');">
                                                    <input type="hidden" name="tabla" value="<?php echo $tabla; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                                </form>
                                            </td>
                                        <?php
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
                                echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&anio=' . $anioFiltro . '&pagina=' . ($paginaActual - 1) . '" class="pagination-link mx-3">Anterior</a>';
                            }
                            for ($i = 1; $i <= $totalPaginas; $i++) {
                                if ($i == $paginaActual) {
                                    echo '<span class="pagination-link current">' . $i . '</span>';
                                } else {
                                    echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&anio=' . $anioFiltro . '&pagina=' . $i . '" class="pagination-link mx-3">' . $i . '</a>';
                                }
                            }
                            if ($paginaActual < $totalPaginas) {
                                echo '<a href="?id=' . $_GET['id'] . '&mes=' . $mesFiltro . '&anio=' . $anioFiltro . '&pagina=' . ($paginaActual + 1) . '" class="pagination-link mx-3">Siguiente</a>';
                            }
                        }
                        ?>
                    </div>
            <?php
                    // Agregar enlace para descargar el archivo Excel
                    echo '<div class="text-center mt-4">';
                    $mes = empty($mesFiltro) ? $mesActual : $mesFiltro;
                    $anio = empty($anioFiltro) ? $anioActual : $anioFiltro;
                    echo '<a href="logic/export.php?id=' . $tabla . '&mes=' . $mes . '&anio=' . $anio . '" class="btn btn-primary">Exportar a Excel</a>';
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
    document.getElementById("mes").addEventListener("change", function() {
        // Obtiene el valor actual del campo de entrada o selección
        var mes = this.value;
        var anio = document.getElementById("anio").value;

        // Obtiene la URL base sin parámetros de consulta
        var url = window.location.href.split('?')[0];

        // Actualiza la URL agregando los parámetros de consulta "id", "mes" y "anio" con los valores correspondientes
        window.location.href = url + '?id=<?php echo $_GET["id"]; ?>&mes=' + mes + '&anio=' + anio;
    });

    // Escucha el evento de cambio en el elemento con el id "anio"
    document.getElementById("anio").addEventListener("change", function() {
        // Obtiene el valor actual del campo de entrada o selección
        var anio = this.value;
        var mes = document.getElementById("mes").value;

        // Obtiene la URL base sin parámetros de consulta
        var url = window.location.href.split('?')[0];

        // Actualiza la URL agregando los parámetros de consulta "id", "mes" y "anio" con los valores correspondientes
        window.location.href = url + '?id=<?php echo $_GET["id"]; ?>&mes=' + mes + '&anio=' + anio;
    });

    // Escucha el evento de cambio en el elemento con el id "csvFile"
    document.getElementById('csvFile').addEventListener('change', function(event) {
        // Pregunta al usuario si desea continuar con la subida del archivo
        var confirmUpload = confirm("¿Desea subir este archivo?");
        if (confirmUpload) {
            document.getElementById('submitBtn').click();
        } else {
            // Si el usuario cancela, limpiar el campo de archivo
            event.target.value = '';
        }
    });
</script>

<?php include 'layouts/footer.php'; ?>