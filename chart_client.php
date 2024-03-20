<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset ($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
include 'db.php';

// Recibo por GET la tabla del cliente a consultar
$tabla = isset ($_GET['tabla']) ? $_GET['tabla'] : '';

// Obtener los meses seleccionados
$mes1 = isset ($_GET['mes1']) ? $_GET['mes1'] : date('n');
$mes2 = isset ($_GET['mes2']) ? $_GET['mes2'] : (date('n') - 1);

// Asegúrate de manejar el cambio de año si es necesario
$añoActual = date('Y');
$año1 = $mes1 >= date('n') ? $añoActual : $añoActual - 1; // Ajustar año si mes1 es en el futuro
$año2 = $mes2 >= date('n') ? $añoActual : $añoActual - 1; // Ajustar año si mes2 es en el futuro

// Obtener el número de días de cada mes seleccionado
$numDiasMes1 = cal_days_in_month(CAL_GREGORIAN, $mes1, $año1);
$numDiasMes2 = cal_days_in_month(CAL_GREGORIAN, $mes2, $año2);

// Usar el mayor número de días para el rango de la gráfica
$numDias = max($numDiasMes1, $numDiasMes2);

// Crear un array para almacenar los días del mes
$labels = array();
for ($dia = 1; $dia <= $numDias; $dia++) {
    $labels[] = $dia;
}

$labelsJSON = json_encode($labels);

// Arrays para almacenar los leads de cada mes
$leadsMes1 = array_fill(0, $numDias, 0); // Rellenar con ceros
$leadsMes2 = array_fill(0, $numDias, 0); // Rellenar con ceros

// Obtener los leads para cada mes seleccionado
for ($dia = 1; $dia <= $numDias; $dia++) {
    if ($dia <= $numDiasMes1) {
        $consultaMes1 = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mes1 AND YEAR(fecha) = $año1";
        $resultado = $conn->query($consultaMes1);
        if ($resultado && $fila = $resultado->fetch_assoc()) {
            $leadsMes1[$dia - 1] = $fila['lead'];
        }
    }

    if ($dia <= $numDiasMes2) {
        $consultaMes2 = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mes2 AND YEAR(fecha) = $año2";
        $resultado = $conn->query($consultaMes2);
        if ($resultado && $fila = $resultado->fetch_assoc()) {
            $leadsMes2[$dia - 1] = $fila['lead'];
        }
    }
}

$leadsMes1JSON = json_encode($leadsMes1);
$leadsMes2JSON = json_encode($leadsMes2);

// Cerrar la conexión a la base de datos
$conn->close();
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-3">Gráfico, mes actual ó seleccionado VS anterior de
                <?= $tabla ?>
            </h4>
            <small>Aquí se vera un comparativo del mes actual contra el mes anterior dependiendo del cliente.</small>
            <br>
            <small>Presiona F5 para ver datos aleatorios en la gráfica.</small>
        </div>
        <div class="col-md-4">
            <select name="mes1" id="mes1" class="form-control my-2">
                <option disabled selected>Selecciona un mes</option>
                <?php
                $mes1Seleccionado = $_GET['mes1'] ?? date('m');  // Valor por defecto para el primer selector
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
                    echo '<option value="' . $mesNumero . '"' . ($mes1Seleccionado == $mesNumero ? ' selected' : '') . '>' . $nombreMes . '</option>';
                }
                ?>
            </select>

            <select name="mes2" id="mes2" class="form-control my-2">
                <option disabled selected>Selecciona un mes</option>
                <?php
                $mes2Seleccionado = $_GET['mes2'] ?? date('m');  // Valor por defecto para el primer selector
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
                $mes1Nombre = $nombresMeses[sprintf("%02d", $mes1)];
                $mes2Nombre = $nombresMeses[sprintf("%02d", $mes2)];
                foreach ($nombresMeses as $mesNumero => $nombreMes) {
                    echo '<option value="' . $mesNumero . '"' . ($mes2Seleccionado == $mesNumero ? ' selected' : '') . '>' . $nombreMes . '</option>';
                }
                ?>
            </select>

            <script>
                const selectMes1 = document.getElementById('mes1');
                const selectMes2 = document.getElementById('mes2');

                const actualizarPaginaConMeses = () => {
                    const tabla = '<?php echo $tabla; ?>'; // Asegúrate de que esta variable se ha definido correctamente en tu script PHP
                    window.location.href = `chart_client.php?mes1=${selectMes1.value}&mes2=${selectMes2.value}&tabla=${tabla}`;
                };

                selectMes1.addEventListener('change', actualizarPaginaConMeses);
                selectMes2.addEventListener('change', actualizarPaginaConMeses);
            </script>
        </div>

    </div>

    <canvas class="mb-5" id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $labelsJSON; ?>,
            datasets: [{
                label: '<?php echo $mes1Nombre; ?>',
                data: <?php echo $leadsMes1JSON; ?>,
                backgroundColor: 'rgba(239, 72, 46, 0.8)',
                borderColor: 'rgba(239, 72, 46, 1)',
                fill: false
            }, {
                label: '<?php echo $mes2Nombre; ?>',
                data: <?php echo $leadsMes2JSON; ?>,
                backgroundColor: 'rgba(29, 158, 245, 0.8)',
                borderColor: 'rgba(29, 158, 245, 1)',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Comparación de Leads por Día'
                }
            }
        }
    });
</script>


<?php include 'layouts/footer.php'; ?>