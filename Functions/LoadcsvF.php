<?php
// functions.php
include 'db.php';

function cargarCSV($file) {
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        $data = [];
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
        return ['header' => $header, 'data' => $data];
    } else {
        throw new Exception("Error al abrir el archivo.");
    }
}

function mapearCabeceras($post) {
    $mapeo = [];
    foreach ($post as $key => $value) {
        if (strpos($key, 'map_') === 0) {
            $db_column = substr($key, 4);
            $csv_column = $value;
            $mapeo[$db_column] = $csv_column;
        }
    }
    return $mapeo;
}

function cargarDatosEnBD($tabla, $mapeo, $header_csv, $csv_data) {
    global $conn;
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
            $check_sql = "SELECT id FROM $tabla WHERE id = '$id_value'";
            $result = $conn->query($check_sql);

            if ($result && $result->num_rows > 0) {
                $update_sql = "UPDATE $tabla SET " . implode(", ", $update_values) . " WHERE id = '$id_value'";
                if (!$conn->query($update_sql)) {
                    echo "Error al actualizar el registro: " . $conn->error;
                }
            } else {
                $insert_sql = "INSERT INTO $tabla (" . implode(",", $sql_columns) . ") VALUES (" . implode(",", $sql_values) . ")";
                if (!$conn->query($insert_sql)) {
                    echo "Error al insertar el registro: " . $conn->error;
                }
            }
        }
    }
}

function obtenerCabecerasTabla($tabla) {
    global $conn;
    $sql = "SHOW COLUMNS FROM $tabla";
    $result = $conn->query($sql);

    $header_db = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $header_db[] = $row['Field'];
        }
    } else {
        throw new Exception("No se encontraron cabeceras en la tabla $tabla.");
    }
    return $header_db;
}
?>
