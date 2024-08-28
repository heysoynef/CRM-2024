<?php
session_start(); // Iniciar sesión
require 'Functions/ChartF.php'; // Incluir funciones
// Verificar sesión y redirigir si es necesario
verificarSesion();


require 'layouts/header.php'; // Encabezado



// Obtener nombres de tablas y datos
$tablas = obtenerTablas();
$labels = array_map('strtoupper', $tablas);

// Obtener mes y año seleccionados o usar los actuales
$currentMonth = date('m');
$currentYear = date('Y');
$selectedMonth = isset($_GET['mes']) ? $_GET['mes'] : $currentMonth;
$selectedYear = isset($_GET['anio']) ? $_GET['anio'] : $currentYear;

// Obtener datos de leads por mes y año
$leads = array_map(function($tabla) use ($selectedMonth, $selectedYear) {
    return obtenerLeadsPorMesYAnio($tabla, $selectedMonth, $selectedYear);
}, $tablas);
?>

<!-- HTML para mostrar la interfaz del gráfico -->
<div class="container">
    <div class="row text-center">
        <div class="col-md-12">
            <h3 class="mb-3">Gráfico General</h3>
            <small>Aquí se muestra una gráfica general del mes en curso o del que hayas seleccionado.</small>
        </div>
    </div>
    <div class="row d-flex justify-content-center">
        <div class="col-md-3"></div>
        <div class="col-md-2">
            <form id="formMes" action="" method="get">
                <select name="mes" id="mes" class="form-control my-2" style="font-size: 14px;">
                    <option disabled>Selecciona un mes</option>
                    <option value="01" <?php if ($selectedMonth === '01') echo 'selected'; ?>>Enero</option>
                    <option value="02" <?php if ($selectedMonth === '02') echo 'selected'; ?>>Febrero</option>
                    <option value="03" <?php if ($selectedMonth === '03') echo 'selected'; ?>>Marzo</option>
                    <option value="04" <?php if ($selectedMonth === '04') echo 'selected'; ?>>Abril</option>
                    <option value="05" <?php if ($selectedMonth === '05') echo 'selected'; ?>>Mayo</option>
                    <option value="06" <?php if ($selectedMonth === '06') echo 'selected'; ?>>Junio</option>
                    <option value="07" <?php if ($selectedMonth === '07') echo 'selected'; ?>>Julio</option>
                    <option value="08" <?php if ($selectedMonth === '08') echo 'selected'; ?>>Agosto</option>
                    <option value="09" <?php if ($selectedMonth === '09') echo 'selected'; ?>>Septiembre</option>
                    <option value="10" <?php if ($selectedMonth === '10') echo 'selected'; ?>>Octubre</option>
                    <option value="11" <?php if ($selectedMonth === '11') echo 'selected'; ?>>Noviembre</option>
                    <option value="12" <?php if ($selectedMonth === '12') echo 'selected'; ?>>Diciembre</option>
                </select>
            </form>
        </div>
        <div class="col-md-2">
            <form id="formAnio" action="" method="get">
                <select name="anio" id="anio" class="form-control my-2" style="font-size: 14px;">
                    <option disabled>Selecciona un año</option>
                    <?php
                    for ($i = 2020; $i <= date('Y'); $i++) {
                        $anioSeleccionadoHTML = ($selectedYear == $i) ? 'selected' : '';
                        printf('<option value="%s" %s>%s</option>', $i, $anioSeleccionadoHTML, $i);
                    }
                    ?>
                </select>
            </form>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <button type="button" class="btn btn-primary" onclick="submitForms()">Filtrar</button>
        </div>
    </div>
    <canvas class="mb-5" id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function submitForms() {
        const formMes = document.getElementById('formMes');
        const formAnio = document.getElementById('formAnio');

        const mes = formMes.mes.value;
        const anio = formAnio.anio.value;

        if (mes && anio) {
            const url = `?mes=${mes}&anio=${anio}`;
            window.location.href = url;
        } else {
            alert("Por favor selecciona un mes y un año.");
        }
    }

    const ctx = document.getElementById('myChart');
    const labels = <?php echo json_encode($labels); ?>;
    const leads = [<?php echo implode(',', $leads); ?>];

    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '# Leads',
                data: leads,
                backgroundColor: 'rgba(239, 72, 46, 0.8)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const chartElement = elements[0];
                    const label = myChart.data.labels[chartElement.index].toLowerCase(); // Convertir a minúsculas
                    const url = `http://localhost/crm-2024/data.php?id=${label}&mes=<?php echo $selectedMonth; ?>&anio=<?php echo $selectedYear; ?>`;
                    window.location.href = url;
                }
            }
        }
    });
</script>

<?php
include 'layouts/footer.php'; // Pie de página
$conn->close(); // Cerrar conexión a la base de datos
?>
