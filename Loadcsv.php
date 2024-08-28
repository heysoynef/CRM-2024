<?php
include 'Functions/LoadcsvF.php';
session_start();

// Verificar si el parámetro 'tabla' está presente
if (!isset($_GET['tabla']) && !isset($_POST['tabla'])) {
    die("Error: Parámetro 'tabla' no encontrado en la URL.");
}

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : $_POST['tabla'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file'])) {
        try {
            $file = $_FILES['file']['tmp_name'];
            $csv_content = cargarCSV($file);

            $_SESSION['header'] = $csv_content['header'];
            $_SESSION['csv_data'] = $csv_content['data'];

            header("Location: index.php?step=map&tabla=" . urlencode($tabla));
            exit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    } elseif (isset($_POST['map'])) {
        if (!isset($_SESSION['header']) || !isset($_SESSION['csv_data'])) {
            echo "No hay datos para procesar.";
            exit();
        }

        $header_csv = $_SESSION['header'];
        $csv_data = $_SESSION['csv_data'];
        $mapeo = mapearCabeceras($_POST);

        cargarDatosEnBD($tabla, $mapeo, $header_csv, $csv_data);

        echo "Datos cargados exitosamente.<br><br>";
        $mes_actual = date('m');
        echo '<a href="/crm-2024/data.php?id=' . $_POST['tabla'] . '&mes=' . $mes_actual . '" style="text-decoration: none;">Regresar al inicio</a>';
        exit();
    }
} else {
    if (isset($_GET['step']) && $_GET['step'] == 'map' && isset($_SESSION['header'])) {
        try {
            $header_csv = $_SESSION['header'];
            $header_db = obtenerCabecerasTabla($tabla);
?>
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Mapeo de Cabeceras del CSV a la Tabla <?= htmlspecialchars($tabla) ?></title>
                <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            </head>

            <body>
                <div class="container">
                    <h1 class="mt-5">Mapeo de Cabeceras del CSV a la Tabla <?= htmlspecialchars($tabla) ?></h1>
                    <form action="index.php" method="post">
                        <input type="hidden" name="tabla" value="<?= htmlspecialchars($tabla) ?>">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Cabeceras de la Tabla <?= htmlspecialchars($tabla) ?></th>
                                    <th>Seleccione la Cabecera del CSV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_mapeado = false;
                                foreach ($header_db as $column) :
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($column); ?></td>
                                        <td>
                                            <?php if ($column == 'id') : ?>
                                                <select name="map_<?php echo htmlspecialchars($column); ?>" class="form-control">
                                                    <option value="">-- Ninguno --</option>
                                                    <?php if (in_array('id', $header_csv)) : ?>
                                                        <option value="id">id</option>
                                                        <?php $id_mapeado = true; ?>
                                                    <?php endif; ?>
                                                </select>
                                            <?php else : ?>
                                                <select name="map_<?php echo htmlspecialchars($column); ?>" class="form-control">
                                                    <option value="">-- Ninguno --</option>
                                                    <?php foreach ($header_csv as $csv_column) : ?>
                                                        <?php if ($csv_column !== 'id' || !$id_mapeado) : ?>
                                                            <option value="<?php echo htmlspecialchars($csv_column); ?>">
                                                                <?php echo htmlspecialchars($csv_column); ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="map" class="btn btn-primary mt-3">Mapear Cabeceras</button>
                    </form>
                </div>
            </body>

            </html>
        <?php
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Subir Archivo CSV</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body>
            <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
                <form action="index.php?tabla=<?= htmlspecialchars($tabla) ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Subir archivo CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                    </div>
                    <input type="hidden" name="tabla" value="<?= htmlspecialchars($tabla) ?>">
                    <button type="submit" class="btn btn-primary">Subir archivo</button>
                </form>
            </div>
        </body>

        </html>
<?php
    }
}
?>