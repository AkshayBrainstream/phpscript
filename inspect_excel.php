<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the Excel file
$excelFile = 'GEPS SEM 5 and 6.xlsx';
echo "Reading Excel file: $excelFile\n";
echo str_repeat('=', 80) . "\n\n";

$spreadsheet = IOFactory::load($excelFile);

// Get all sheet names
$sheetNames = $spreadsheet->getSheetNames();
echo "Available sheets: " . implode(', ', $sheetNames) . "\n\n";

// Process each sheet
foreach ($sheetNames as $sheetName) {
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    echo "Sheet: $sheetName\n";
    echo str_repeat('-', 80) . "\n";

    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();

    echo "Dimensions: Rows: $highestRow, Columns: $highestColumn\n\n";

    // Display first 20 rows
    $maxRowsToShow = min(20, $highestRow);

    for ($row = 1; $row <= $maxRowsToShow; $row++) {
        $rowData = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            if (!empty($cellValue)) {
                $rowData[] = "$col$row: " . substr($cellValue, 0, 50);
            }
        }
        if (!empty($rowData)) {
            echo "Row $row: " . implode(' | ', $rowData) . "\n";
        }
    }

    echo "\n" . str_repeat('=', 80) . "\n\n";
}

echo "Inspection complete!\n";
?>
