<?php
session_start();

include 'layouts/header.php';
include 'db.php';

// Obtener la lista de nombres de tablas de la base de datos
$query = "SHOW TABLES";
$result = mysqli_query($conn, $query);
$tables = array();
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// Code para establecer el mes actual en el input select
if (isset($_GET['mes'])) {
    $selectedMonth = $_GET['mes'];
} else {
    $selectedMonth = date('m'); // Establecer el mes actual como predeterminado
}

function obtenerConteoRegistros($tabla, $selectedMonth)
{
    global $conn; // Accede a la conexión global en esta función

    // $mesActual = date('Y-m');
    // $query = "SELECT COUNT(*) AS conteo FROM $tabla WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mesActual'";

    $query = "SELECT COUNT(*) AS conteo FROM $tabla WHERE MONTH(fecha) = $selectedMonth";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['conteo'];
    }

    return 0;
}
?>

<div class="container" style="height: 100vh;">
    <div class="row mt-3">
        <div class="col-md-8">
            <p>Aquí se muentras los clientes activos dentro de la base de datos del CRM.</p>
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
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Leads</th>
                        <th>Listado</th>
                        <th>Gráfico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table) : ?>
                        <?php if ($table !== "users") : ?>
                            <tr>
                                <td><?php echo $table; ?></td>
                                <td><?php echo obtenerConteoRegistros($table, $selectedMonth); ?></td>
                                <td><a class="text-primary" href="data.php?id=<?php echo $table; ?>&mes=<?php echo $selectedMonth; ?>">Listado</a></td>
                                <td><a class="text-success" href="chart_client.php?tabla=<?php echo $table; ?>">Gráfico</a></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- VALIDACIÓN -->
                    <script>
                        function verificarPermisos() {
                            var sessionType = "<?php echo $sessionType; ?>";

                            if (sessionType !== "Super_Admin") {
                                alert("No tienes permisos para realizar esta acción.");
                                return false; // Previene el evento de eliminación
                            }

                            return true; // Permite el evento de eliminación
                        }
                    </script>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include 'layouts/footer.php';
?>