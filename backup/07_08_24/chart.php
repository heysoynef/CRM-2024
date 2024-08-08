<?php
session_start(); // Iniciar sesión

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Verificar si el usuario tiene el tipo "cliente"
if ($_SESSION["type"] === "Cliente") {
    // Obtener el nombre del campo de cliente del usuario
    $nombre_campo_cliente = obtenerNombreCampoCliente($_SESSION["id"]); // Ajusta esto según tu sistema

    // Redirigir al usuario a chart.php con los parámetros adecuados
    header("Location: chart_client.php?tabla=" . urlencode($nombre_campo_cliente));
    exit();
}

include 'layouts/header.php';
include 'db.php';

$tablas = array();

// Consulta SQL para obtener los nombres de las tablas desde la base de datos
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_row()) {
        $tabla = $row[0];
        if ($tabla != 'users') {
            $tablas[] = $tabla; // Agrega el nombre de la tabla al array $tablas, excluyendo la tabla "users"
        }
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

$labels = array_map('strtoupper', $tablas);

$leads = array();

$currentMonth = date('m'); // Obtener el número del mes actual
$currentYear = date('Y'); // Obtener el año actual

$selectedMonth = isset($_GET['mes']) ? $_GET['mes'] : $currentMonth; // Establecer el mes actual como predeterminado si no se ha seleccionado
$selectedYear = isset($_GET['anio']) ? $_GET['anio'] : $currentYear; // Establecer el año actual como predeterminado si no se ha seleccionado

// Consulta SQL para obtener el número de registros en el mes y año seleccionados para cada tabla
foreach ($tablas as $tabla) {
    $sql = "SELECT COUNT(*) AS total FROM $tabla WHERE MONTH(fecha) = $selectedMonth AND YEAR(fecha) = $selectedYear";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $leads[] = $row["total"];
    } else {
        $leads[] = 0; // Si no hay registros, se asigna 0
    }
}

$conn->close();
?>


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
                    <option value="01" <?php if ($selectedMonth === '01')
                        echo 'selected'; ?>>Enero</option>
                    <option value="02" <?php if ($selectedMonth === '02')
                        echo 'selected'; ?>>Febrero</option>
                    <option value="03" <?php if ($selectedMonth === '03')
                        echo 'selected'; ?>>Marzo</option>
                    <option value="04" <?php if ($selectedMonth === '04')
                        echo 'selected'; ?>>Abril</option>
                    <option value="05" <?php if ($selectedMonth === '05')
                        echo 'selected'; ?>>Mayo</option>
                    <option value="06" <?php if ($selectedMonth === '06')
                        echo 'selected'; ?>>Junio</option>
                    <option value="07" <?php if ($selectedMonth === '07')
                        echo 'selected'; ?>>Julio</option>
                    <option value="08" <?php if ($selectedMonth === '08')
                        echo 'selected'; ?>>Agosto</option>
                    <option value="09" <?php if ($selectedMonth === '09')
                        echo 'selected'; ?>>Septiembre</option>
                    <option value="10" <?php if ($selectedMonth === '10')
                        echo 'selected'; ?>>Octubre</option>
                    <option value="11" <?php if ($selectedMonth === '11')
                        echo 'selected'; ?>>Noviembre</option>
                    <option value="12" <?php if ($selectedMonth === '12')
                        echo 'selected'; ?>>Diciembre</option>
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
include 'layouts/footer.php';
?>


<?php
include 'layouts/footer.php';
?>