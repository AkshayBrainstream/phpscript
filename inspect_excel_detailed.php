<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the Excel file
$excelFile = 'GEPS SEM 5 and 6.xlsx';
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();

$highestColumn = $worksheet->getHighestColumn();

echo "Column Headers (Row 2):\n";
echo str_repeat('=', 100) . "\n";

// Display headers from row 2
for ($col = 'A'; $col <= $highestColumn; $col++) {
    $cellValue = $worksheet->getCell($col . '2')->getValue();
    if (!empty($cellValue)) {
        echo sprintf("%-5s: %s\n", $col . '2', $cellValue);
    }
}

echo "\n" . str_repeat('=', 100) . "\n";
echo "\nFirst Student Data (Row 3):\n";
echo str_repeat('=', 100) . "\n";

// Display first student data
for ($col = 'A'; $col <= $highestColumn; $col++) {
    $header = $worksheet->getCell($col . '2')->getValue();
    $value = $worksheet->getCell($col . '3')->getValue();
    if (!empty($header) || !empty($value)) {
        echo sprintf("%-5s %-40s: %s\n", $col . '3', $header ?? '', $value ?? '');
    }
}
?>
