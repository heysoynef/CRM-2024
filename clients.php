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

function obtenerConteoRegistros($tabla)
{
    global $conn; // Accede a la conexión global en esta función

    $mesActual = date('Y-m');
    $query = "SELECT COUNT(*) AS conteo FROM $tabla WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mesActual'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['conteo'];
    }

    return 0;
}
?>

<div class="container">
    <div class="row mt-3">
        <div class="col">
            <p>Aquí se muentras los clientes activos dentro de la base de datos del CRM.</p>
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
                                <td><?php echo obtenerConteoRegistros($table); ?></td>
                                <td><a class="text-primary" href="data.php?id=<?php echo $table; ?>">Listado</a></td>
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