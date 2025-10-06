<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFile = 'GEPS SEM 5 and 6.xlsx';
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();

echo "First 5 rows and 15 columns:\n";
echo str_repeat('=', 120) . "\n\n";

for ($row = 1; $row <= 5; $row++) {
    echo "Row $row:\n";
    for ($col = 'A'; $col <= 'O'; $col++) {
        $cellValue = $worksheet->getCell($col . $row)->getValue();
        if ($cellValue !== null && $cellValue !== '') {
            echo sprintf("  %3s: %s\n", $col . $row, $cellValue);
        }
    }
    echo "\n";
}
?>
