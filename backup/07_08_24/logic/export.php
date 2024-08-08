<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Obtener el ID de la imagen enviado por la URL
$tabla = $_GET["id"];
$mes = $_GET["mes"];

// Realizar la consulta a la base de datos utilizando el valor de $tabla
// Recuerda tener una conexión establecida a la base de datos previamente
include '../db.php';

// Incluir los archivos necesarios de PhpSpreadsheet
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ejemplo de consulta y obtención de resultados utilizando mysqli
$query = "SELECT * FROM $tabla WHERE MONTH(fecha) = $mes";
$result = mysqli_query($conn, $query);

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$rowIndex = 1;
$columnIndex = 1;

// Obtener los datos de la tabla y escribirlos en la hoja de cálculo
$row = mysqli_fetch_assoc($result);
foreach ($row as $column => $value) {
    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $column);
    $columnIndex++;
}

$rowIndex++;

mysqli_data_seek($result, 0); // Reiniciar el puntero del resultado
while ($row = mysqli_fetch_assoc($result)) {
    $columnIndex = 1;
    foreach ($row as $column => $value) {
        if ($column == 'mensaje') {
            $truncatedValue = (strlen($value) > 30) ? substr($value, 0, 30) . '...' : $value;
            $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $truncatedValue);
        } else {
            $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
        }
        $columnIndex++;
    }
    $rowIndex++;
}

// Crear un escritor para generar el archivo Excel
$writer = new Xlsx($spreadsheet);

// Establecer las cabeceras de respuesta para descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="datos.xlsx"');
header('Cache-Control: max-age=0');

// Enviar el archivo al navegador
$writer->save('php://output');

// Cerrar la conexión a la base de datos
mysqli_close($conn);
