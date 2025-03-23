<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ['Student Code', 'First Name', 'Last Name', 'Email', 'Phone', 'Class Code'];
foreach ($headers as $col => $header) {
    $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
}

// Add sample data
$sampleData = [
    ['2024001', 'John', 'Doe', 'john.doe@example.com', '0123456789', 'CS2024'],
];

foreach ($sampleData as $row => $data) {
    foreach ($data as $col => $value) {
        $sheet->setCellValueByColumnAndRow($col + 1, $row + 2, $value);
    }
}

// Auto-size columns
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set header styles
$headerStyle = [
    'font' => ['bold' => true],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'CCCCCC']]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="student_import_template.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;