<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelHelper {
    public static function readExcelFile($filePath) {
        $spreadsheet = IOFactory::load($filePath);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    }

    public static function validateStudentData($data) {
        $validatedData = [];
        foreach ($data as $row) {
            if (!empty($row['A']) && !empty($row['B']) && !empty($row['C'])) {
                $validatedData[] = [
                    'student_code' => $row['A'],
                    'last_name' => $row['B'],
                    'first_name' => $row['C'],
                    'phone' => $row['D'] ?? null,
                    'email' => $row['E'] ?? null,
                    'major' => $row['F'] ?? null,
                    'dob' => $row['G'] ?? null,
                    'class_code' => $row['H'] ?? null,
                ];
            }
        }
        return $validatedData;
    }
}
?>