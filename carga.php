<?php
if (isset($_POST['submit'])) {
    include 'db.php';

    // Cargar la configuración desde el archivo PHP
    $config = include 'cargaconf.php';

    // Obtener el valor del cliente desde el formulario
    $clienteId = $_POST['clienteId']; //prueba

    // Verificar si la configuración del cliente existe
    if (!isset($config['clientes'][$clienteId])) {
        die("Configuración del cliente no encontrada.");
    }

    // Obtener la configuración de campos del cliente
    $campos = $config['clientes'][$clienteId]; // Array de campos
    $numCampos = count($campos);//conteo de valores para consultas dinamicas 

    // Verificar si el archivo fue subido sin errores
    if ($_FILES['csvFile']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['csvFile']['tmp_name'];
        $fileName = $_FILES['csvFile']['name'];
        $fileSize = $_FILES['csvFile']['size'];
        $fileType = $_FILES['csvFile']['type'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Validar que el archivo sea un CSV
        if ($fileExtension === 'csv') {
            if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                // Saltar la primera línea si contiene los encabezados
                fgetcsv($handle, 1000, ",");

                // Construir la consulta de inserción dinámicamente
                $placeholders = implode(',', array_fill(0, $numCampos, '?'));
                $sql = "INSERT INTO " . $clienteId . " (" . implode(',', $campos) . ") VALUES ($placeholders)";
                $stmt = $conn->prepare($sql);

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) == $numCampos) {
                        // Vincular parámetros y ejecutar la consulta
                        $types = str_repeat('s', $numCampos); // Asumimos que todos los campos son strings
                        $stmt->bind_param($types, ...$data);
                        if (!$stmt->execute()) {
                            echo "Error al ejecutar la consulta: " . $stmt->error . "<br>";
                        }
                    } else {
                        echo "La línea CSV no tiene el número correcto de campos: " . implode(",", $data) . "<br>";
                    }
                }

                fclose($handle);
            } else {
                echo "Error al abrir el archivo.";
            }
        } else {
            echo "Por favor, sube un archivo CSV válido.";
        }
    } else {
        echo "Error al subir el archivo: " . $_FILES['csvFile']['error'];
    }

    $conn->close();

    // Obtener el mes actual en formato de dos dígitos
    $mesActual = date('m');

    // Construir la URL con los parámetros necesarios
    $url = "http://localhost/crm-2024/data.php?id={$clienteId}&mes={$mesActual}";

    // Redirigir a la URL construida
    header("Location: {$url}");
    exit();
}

?>
