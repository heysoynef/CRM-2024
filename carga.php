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
    $numCampos = count($campos); // Conteo de valores para consultas dinámicas 

    // Lista de correos y dominios excluidos
    $excludedEmails = [
        'papitaspavel@gmail.com',
        '@monkeysolutions.mx'
    ];

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

                // Iniciar la transacción
                $conn->begin_transaction();

                try {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (count($data) == $numCampos) {
                            // Comprobar si el correo electrónico está en la lista de exclusión
                            $email = $data[2]; // Suponiendo que el campo de email es el tercero
                            $exclude = false;
                            foreach ($excludedEmails as $excludedEmail) {
                                if (strpos($email, $excludedEmail) !== false) {
                                    $exclude = true;
                                    break;
                                }
                            }
                            if ($exclude) {
                                continue; // Saltar esta línea y continuar con la siguiente
                            }

                            // Convertir el campo de fecha al formato MySQL
                            $dateTime = DateTime::createFromFormat('d/m/Y H:i', $data[1]);
                            if ($dateTime) {
                                $data[1] = $dateTime->format('Y-m-d H:i:s');
                            } else {
                                throw new Exception("Formato de fecha incorrecto: " . $data[1]);
                            }

                            // Vincular parámetros y ejecutar la consulta
                            $types = str_repeat('s', $numCampos); // Asumimos que todos los campos son strings
                            $stmt->bind_param($types, ...$data);
                            if (!$stmt->execute()) {
                                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
                            }
                        } else {
                            echo "La línea CSV no tiene el número correcto de campos: " . implode(",", $data) . "<br>";
                        }
                    }

                    // Confirmar la transacción
                    $conn->commit();
                } catch (Exception $e) {
                    // Revertir la transacción en caso de error
                    $conn->rollback();
                    echo $e->getMessage();
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
