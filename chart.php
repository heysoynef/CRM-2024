<?php
session_start(); // Iniciar sesión

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Verificar si el usuario tiene el tipo "cliente"
if ($_SESSION["type"] === "Cliente" ) {
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

function obtenerNombreCampoCliente($id_usuario) {
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

if (isset($_GET['mes'])) {
    $selectedMonth = $_GET['mes'];
} else {
    $selectedMonth = $currentMonth; // Establecer el mes actual como predeterminado
}
 
// Consulta SQL para obtener el número de registros en el mes seleccionado para cada tabla
foreach ($tablas as $tabla) {
    $sql = "SELECT COUNT(*) AS total FROM $tabla WHERE MONTH(fecha) = $selectedMonth";

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
    <div class="row">
        <div class="col-md-8">
            <h3 class="mb-3">Gráfico General</h3>
            <small>Aquí se muestra una gráfica general del mes en curso o del que hayas seleccionado.</small>
        </div>
        <div class="col-md-4">
            <form action="" method="get">
                <select name="mes" id="mes" class="form-control my-2" onchange="this.form.submit()">
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
    </div>

    <canvas class="mb-5" id="myChart"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    const backgroundColor = Array.from({
        length: <?php echo count($labels); ?>
    }, () => 'rgba(239, 72, 46, 0.8)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: '# Leads',
                data: [<?php echo implode(',', $leads); ?>],
                backgroundColor: backgroundColor, // Asignar el color único a todas las barras
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Kumbh Sans', // Cambiar la fuente a 'Kumbh Sans'
                        },
                        precision: 0 // Mostrar valores enteros
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Kumbh Sans', // Cambiar la fuente a 'Kumbh Sans'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Kumbh Sans', // Cambiar la fuente a 'Kumbh Sans'
                        }
                    }
                },
                tooltip: {
                    titleFont: {
                        family: 'Kumbh Sans', // Cambiar la fuente a 'Kumbh Sans'
                        weight: 'lighter'
                    },
                    bodyFont: {
                        family: 'Kumbh Sans', // Cambiar la fuente a 'Kumbh Sans'
                    }
                }
            }
        }
    });
</script>
<!-- Formulario para subir el archivo CSV -->

<?php
include 'layouts/footer.php';
?>


