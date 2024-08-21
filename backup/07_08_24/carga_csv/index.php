<?php
include '../db.php';
session_start();

// Verificar si el parámetro 'tabla' está presente
if (!isset($_GET['tabla']) && !isset($_POST['tabla'])) {
    die("Error: Parámetro 'tabla' no encontrado en la URL.");
}

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : $_POST['tabla'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file'])) {
        // Procesar el archivo CSV
        $file = $_FILES['file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            $data = [];
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);

            // Guardar la cabecera y los datos en sesión para usarlos en la página siguiente
            $_SESSION['header'] = $header;
            $_SESSION['csv_data'] = $data;

            // Redirigir a la misma página para el mapeo de cabeceras
            header("Location: index.php?step=map&tabla=" . urlencode($tabla));
            exit();
        } else {
            echo "Error al abrir el archivo.";
        }
    } elseif (isset($_POST['map'])) {
        // Procesar el mapeo y cargar los datos en la base de datos
        if (!isset($_SESSION['header']) || !isset($_SESSION['csv_data'])) {
            echo "No hay datos para procesar.";
            exit();
        }

        $header_csv = $_SESSION['header'];
        $csv_data = $_SESSION['csv_data'];

        $mapeo = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'map_') === 0) {
                $db_column = substr($key, 4); // Obtener el nombre de la columna de la BD
                $csv_column = $value; // Obtener el nombre de la columna del CSV seleccionado
                $mapeo[$db_column] = $csv_column;
            }
        }

        // Ahora $mapeo contiene el mapeo de columnas de la BD a columnas del CSV
        // Procesar el CSV y cargar los datos en la tabla de bd

        foreach ($csv_data as $row) {
            $sql_columns = [];
            $sql_values = [];
            $update_values = [];
            $id_value = '';

            foreach ($mapeo as $db_column => $csv_column) {
                $csv_index = array_search($csv_column, $header_csv);
                $value = $conn->real_escape_string($row[$csv_index]);
                if ($db_column == 'id') {
                    $id_value = $value;
                }
                $sql_columns[] = $db_column;
                $sql_values[] = "'$value'";
                if ($db_column != 'id') {
                    $update_values[] = "$db_column = '$value'";
                }
            }

            if (!empty($id_value)) {
                // Verificar si el ID ya existe
                $check_sql = "SELECT id FROM $tabla WHERE id = '$id_value'";
                $result = $conn->query($check_sql);

                if ($result && $result->num_rows > 0) {
                    // El ID existe, actualizar el registro
                    $update_sql = "UPDATE $tabla SET " . implode(", ", $update_values) . " WHERE id = '$id_value'";
                    if (!$conn->query($update_sql)) {
                        echo "Error al actualizar el registro: " . $conn->error;
                    }
                } else {
                    // El ID no existe, insertar un nuevo registro
                    $insert_sql = "INSERT INTO $tabla (" . implode(",", $sql_columns) . ") VALUES (" . implode(",", $sql_values) . ")";
                    if (!$conn->query($insert_sql)) {
                        echo "Error al insertar el registro: " . $conn->error;
                    }
                }
            }
        }

        echo "Datos cargados exitosamente.<br><br>";
        echo '<a href="index.php?tabla=' . urlencode($tabla) . '" style="text-decoration: none;">Regresar al inicio</a>';
        session_destroy();
        exit();
    }
} else {
    // Mostrar el formulario de subida de archivo
    if (isset($_GET['step']) && $_GET['step'] == 'map' && isset($_SESSION['header'])) {
        // Mostrar el formulario de mapeo de cabeceras
        $header_csv = $_SESSION['header'];

        $sql = "SHOW COLUMNS FROM $tabla";
        $result = $conn->query($sql);

        $header_db = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $header_db[] = $row['Field'];
            }
        } else {
            echo "No se encontraron cabeceras en la tabla $tabla.";
            exit();
        }
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
                            <?php foreach ($header_db as $column) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($column); ?></td>
                                    <td>
                                        <select name="map_<?php echo htmlspecialchars($column); ?>" class="form-control">
                                            <?php foreach ($header_csv as $csv_column) : ?>
                                                <option value="<?php echo htmlspecialchars($csv_column); ?>">
                                                    <?php echo htmlspecialchars($csv_column); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
    } else {
        // Mostrar el formulario de subida de archivo
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