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

// Obtener los meses y años seleccionados
$mes1 = isset ($_GET['mes1']) ? $_GET['mes1'] : date('n');
$mes2 = isset ($_GET['mes2']) ? $_GET['mes2'] : (date('n') - 1);
$anio1 = isset ($_GET['anio1']) ? $_GET['anio1'] : date('Y');
$anio2 = isset ($_GET['anio2']) ? $_GET['anio2'] : date('Y');

// Obtener el número de días de cada mes seleccionado
$numDiasMes1 = cal_days_in_month(CAL_GREGORIAN, $mes1, $anio1);
$numDiasMes2 = cal_days_in_month(CAL_GREGORIAN, $mes2, $anio2);

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
        $consultaMes1 = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mes1 AND YEAR(fecha) = $anio1";
        $resultado = $conn->query($consultaMes1);
        if ($resultado && $fila = $resultado->fetch_assoc()) {
            $leadsMes1[$dia - 1] = $fila['lead'];
        }
    }

    if ($dia <= $numDiasMes2) {
        $consultaMes2 = "SELECT COUNT(*) AS lead FROM $tabla WHERE DAY(fecha) = $dia AND MONTH(fecha) = $mes2 AND YEAR(fecha) = $anio2";
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
        <div class="col-md-6">
            <h4 class="mb-3">Gráfico, mes actual o seleccionado VS anterior de
                <?= $tabla ?>
            </h4>
            <small>Aquí se vera un comparativo del mes actual contra el mes anterior dependiendo del cliente.</small>
            <br>
            <small>Presiona F5 para ver datos aleatorios en la gráfica.</small>
        </div>
        <div class="col-md-6">  
        <select name="mes1" id="mes1" class="form-control my-2">
                <option disabled selected>Selecciona un mes</option>
                <?php
                $mes1Seleccionado = $_GET['mes1'] ?? date('n'); // Valor por defecto para el primer selector
                $nombresMeses = array(
                    '1' => 'Enero',
                    '2' => 'Febrero',
                    '3' => 'Marzo',
                    '4' => 'Abril',
                    '5' => 'Mayo',
                    '6' => 'Junio',
                    '7' => 'Julio',
                    '8' => 'Agosto',
                    '9' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre'
                );
                foreach ($nombresMeses as $mesNumero => $nombreMes) {
                    echo '<option value="' . $mesNumero . '"' . ($mes1Seleccionado == $mesNumero ? ' selected' : '') . '>' . $nombreMes . '</option>';
                }
                ?>
            </select>

            <!-- Selector de año para mes1 -->
            <select name="anio1" id="anio1" class="form-control my-2">
                <option disabled selected>Selecciona un año</option>
                <?php
                $anioActual = date('Y');
                for ($anio = $anioActual - 2; $anio <= $anioActual; $anio++) {
                    echo "<option value='$anio'" . ($anio1 == $anio ? ' selected' : '') . ">$anio</option>";
                }
                ?>
            </select>

            <select name="mes2" id="mes2" class="form-control my-2">
                <option disabled selected>Selecciona un mes</option>
                <?php
                $mes2Seleccionado = $_GET['mes2'] ?? date('n');
                foreach ($nombresMeses as $mesNumero => $nombreMes) {
                    echo '<option value="' . $mesNumero . '"' . ($mes2Seleccionado == $mesNumero ? ' selected' : '') . '>' . $nombreMes . '</option>';
                }
                ?>
            </select>

            <!-- Selector de año para mes2 -->
            <select name="anio2" id="anio2" class="form-control my-2">
                <option disabled selected>Selecciona un año</option>
                <?php
                for ($anio = $anioActual - 2; $anio <= $anioActual; $anio++) {
                    echo "<option value='$anio'" . ($anio2 == $anio ? ' selected' : '') . ">$anio</option>";
                }
                ?>
            </select>

            <script>
                const selectMes1 = document.getElementById('mes1');
                const selectAnio1 = document.getElementById('anio1');
                const selectMes2 = document.getElementById('mes2');
                const selectAnio2 = document.getElementById('anio2');

                const actualizarPaginaConMesesYAnios = () => {
                    const tabla = '<?php echo $tabla; ?>';
                    window.location.href = `chart_client.php?mes1=${selectMes1.value}&anio1=${selectAnio1.value}&mes2=${selectMes2.value}&anio2=${selectAnio2.value}&tabla=${tabla}`;
                };

                selectMes1.addEventListener('change', actualizarPaginaConMesesYAnios);
                selectAnio1.addEventListener('change', actualizarPaginaConMesesYAnios);
                selectMes2.addEventListener('change', actualizarPaginaConMesesYAnios);
                selectAnio2.addEventListener('change', actualizarPaginaConMesesYAnios);
            </script>
        </div>
    </div>

    <canvas id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const labelsJSON = <?php echo $labelsJSON; ?>;
    const leadsMes1JSON = <?php echo $leadsMes1JSON; ?>;
    const leadsMes2JSON = <?php echo $leadsMes2JSON; ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsJSON,
            datasets: [{
                label: 'Mes 1: <?php echo $nombresMeses[$mes1] . " " . $anio1; ?>',
                data: leadsMes1JSON,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'Mes 2: <?php echo $nombresMeses[$mes2] . " " . $anio2; ?>',
                data: leadsMes2JSON,
                fill: false,
                borderColor: 'rgb(153, 102, 255)',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'layouts/footer.php'; ?>