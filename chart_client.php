<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
include 'db.php';

// Recibo por GET la tabla del cliente a consultar
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';

// Obtener el mes y año actual
$mesActual = isset($_GET['mes']) ? $_GET['mes'] : date('n');
$añoActual = date('Y');

// Obtener el número de días del mes actual
$numDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, $añoActual);

// Calcular el mes y año del mes anterior
$mesAnterior = $mesActual - 1;
$añoAnterior = $añoActual;

// Si el mes anterior es menor que 1, ajustar el mes y año
if ($mesAnterior < 1) {
    $mesAnterior = 12;  // Establecer el mes a diciembre
    $añoAnterior--;    // Restar 1 al año
}

// Crear un array para almacenar los días del mes actual
$labels = array();
for ($dia = 1; $dia <= $numDias; $dia++) {
    $labels[] = $dia;
}

// Convertir el array de días a formato JSON
$labelsJSON = json_encode($labels);

// Crear un array para almacenar los leads por día del MES ACTUAL
$leads = array();

// Obtener el recuento de registros por día del MES ACTUAL
for ($dia = 1; $dia <= $numDias; $dia++) {
    // Consulta para obtener el recuento de registros por día del MES ACTUAL
    $consulta = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mesActual AND YEAR(fecha) = $añoActual";
    $resultado = $conn->query($consulta);
    $fila = $resultado->fetch_assoc();
    $recuento = $fila['lead'];

    // Agregar el recuento al array
    $leads[] = $recuento;
}

// Convertir el array de leads a formato JSON
$leadsMesActualJSON = json_encode($leads);

// Crear un array para almacenar los leads por día del MES ANTERIOR
$leadsAnterior = array();

// Obtener el recuento de registros por día del MES ANTERIOR
for ($dia = 1; $dia <= $numDias; $dia++) {
    // Consulta para obtener el recuento de registros por día del MES ANTERIOR
    $consulta = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mesAnterior AND YEAR(fecha) = $añoAnterior";
    $resultado = $conn->query($consulta);
    $fila = $resultado->fetch_assoc();
    $recuento = $fila['lead'];

    // Agregar el recuento al array
    $leadsAnterior[] = $recuento;
}

// Convertir el array de leads del MES ANTERIOR a formato JSON
$leadsMesAnteriorJSON = json_encode($leadsAnterior);

// Cerrar la conexión a la base de datos
$conn->close();
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-3">Gráfico, mes actual ó seleccionado VS anterior de <?= $tabla ?></h4>
            <small>Aquí se vera un comparativo del mes actual contra el mes anterior dependiendo del cliente.</small>
            <br>
            <small>Presiona F5 para ver datos aleatorios en la gráfica.</small>
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

            <script>
                // Obtener el elemento select por su id
                const selectMes = document.getElementById('mes');

                // Agregar un evento change al elemento select
                selectMes.addEventListener('change', function() {
                    const mesSeleccionado = selectMes.value; // Obtener el valor seleccionado

                    // Obtener el valor de la tabla
                    const tabla = '<?php echo $tabla; ?>';

                    // Redirigir a la misma página con el nuevo mes y tabla seleccionados
                    window.location.href = 'chart_client.php?mes=' + mesSeleccionado + '&tabla=' + tabla;
                });
            </script>

        </div>
    </div>

    <canvas class="mb-5" id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    const backgroundColorMesActual = 'rgba(239, 72, 46, 0.8)';
    const backgroundColorMesAnterior = 'rgba(29, 158, 245, 0.8)';
    const lineColorMesActual = 'rgba(239, 72, 46, 1)';
    const lineColorMesAnterior = 'rgba(29, 158, 245, 1)';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $labelsJSON; ?>,
            datasets: [{
                label: 'Mes Actual',
                data: <?php echo $leadsMesActualJSON; ?>,
                backgroundColor: backgroundColorMesActual,
                borderColor: lineColorMesActual,
                fill: false
            }, {
                label: 'Mes Anterior',
                data: <?php echo $leadsMesAnteriorJSON; ?>,
                backgroundColor: backgroundColorMesAnterior,
                borderColor: lineColorMesAnterior,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Kumbh Sans', // Cambio a Kumbh Sans
                        },
                        precision: 0
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Kumbh Sans', // Cambio a Kumbh Sans
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Kumbh Sans', // Cambio a Kumbh Sans
                        }
                    }
                },
                tooltip: {
                    titleFont: {
                        family: 'Kumbh Sans', // Cambio a Kumbh Sans
                        weight: 'lighter'
                    },
                    bodyFont: {
                        family: 'Kumbh Sans', // Cambio a Kumbh Sans
                    }
                }
            }
        }
    });
</script>


<?php
include 'layouts/footer.php'
?>