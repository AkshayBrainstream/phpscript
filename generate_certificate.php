<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the Excel file
$excelFile = 'GEPS SEM 5 and 6.xlsx';
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();

// Select which student row to process (default: first student is row 3)
$studentRow = 3; // Change this to process different students

// Get data from Excel based on the structure:
// A=NAME, B=EN (Enrollment), C=SEM (Semester), D=BR (Branch),
// E=CIM (Card Issue Month), F=CN (Card Number), G=SPI
$studentName = $worksheet->getCell('A' . $studentRow)->getValue();
$enrollmentNumber = $worksheet->getCell('B' . $studentRow)->getValue();
$semester = $worksheet->getCell('C' . $studentRow)->getValue();
$branch = $worksheet->getCell('D' . $studentRow)->getValue();
$cardIssueMonth = $worksheet->getCell('E' . $studentRow)->getValue();
$cardNumber = $worksheet->getCell('F' . $studentRow)->getValue();
$spi = $worksheet->getCell('G' . $studentRow)->getValue();

// Additional academic data
$sdp = $worksheet->getCell('H' . $studentRow)->getValue();
$researchPaper = $worksheet->getCell('I' . $studentRow)->getValue();
$bookPublished = $worksheet->getCell('J' . $studentRow)->getValue();
$patent = $worksheet->getCell('K' . $studentRow)->getValue();
$evaluatorMarks = $worksheet->getCell('L' . $studentRow)->getValue();
$mentorMarks = $worksheet->getCell('M' . $studentRow)->getValue();
$softSkillMarks = $worksheet->getCell('N' . $studentRow)->getValue();
$labTestingSkill = $worksheet->getCell('O' . $studentRow)->getValue();

// Load the base image
$imagePath = 'Format.jpeg';
$image = imagecreatefromjpeg($imagePath);

// Enable alpha blending for better text rendering
imagealphablending($image, true);
imagesavealpha($image, true);

// Define colors
$black = imagecolorallocate($image, 0, 0, 0);
$darkBrown = imagecolorallocate($image, 101, 67, 33);

// Define font path (using default GD font, or specify TTF font path)
// For better quality, download a TTF font and use imagettftext
$fontPath = '/System/Library/Fonts/Helvetica.ttc'; // Mac default font
$fontSize = 16;
$smallFontSize = 12;

// Add text to image at specific positions
// Coordinates are approximate - adjust as needed for perfect alignment

// Header fields
imagettftext($image, $fontSize, 0, 150, 115, $black, $fontPath, $studentName ?? '');
imagettftext($image, $fontSize, 0, 230, 150, $black, $fontPath, $enrollmentNumber ?? '');
imagettftext($image, $fontSize, 0, 440, 150, $black, $fontPath, $semester ?? '');
imagettftext($image, $fontSize, 0, 700, 150, $black, $fontPath, $branch ?? '');
imagettftext($image, $fontSize, 0, 200, 185, $black, $fontPath, '2024-25'); // Academic year
imagettftext($image, $fontSize, 0, 430, 185, $black, $fontPath, $cardIssueMonth ?? '');
imagettftext($image, $fontSize, 0, 700, 185, $black, $fontPath, $cardNumber ?? '');

// Academic scores - SPI/CPI
imagettftext($image, $smallFontSize, 0, 165, 272, $black, $fontPath, $spi ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 272, $black, $fontPath, $spi ?? '0');

// SDP
imagettftext($image, $smallFontSize, 0, 165, 290, $black, $fontPath, $sdp ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 290, $black, $fontPath, $sdp ?? '0');

// Research Paper
imagettftext($image, $smallFontSize, 0, 165, 325, $black, $fontPath, $researchPaper ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 325, $black, $fontPath, $researchPaper ?? '0');

// Patent
imagettftext($image, $smallFontSize, 0, 165, 343, $black, $fontPath, $patent ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 343, $black, $fontPath, $patent ?? '0');

// Book Publication
imagettftext($image, $smallFontSize, 0, 165, 361, $black, $fontPath, $bookPublished ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 361, $black, $fontPath, $bookPublished ?? '0');

// Evaluator marks
imagettftext($image, $smallFontSize, 0, 190, 425, $black, $fontPath, $evaluatorMarks ?? '0');

// Mentor marks
imagettftext($image, $smallFontSize, 0, 190, 525, $black, $fontPath, $mentorMarks ?? '0');

// Save the output image
$outputPath = 'output_certificate.jpg';
imagejpeg($image, $outputPath, 95);

// Clean up
imagedestroy($image);

echo "Certificate generated successfully: $outputPath\n";
echo "Student: $studentName\n";
echo "Enrollment: $enrollmentNumber\n";
echo "Semester: $semester\n";
echo "Branch: $branch\n";
echo "Card Number: $cardNumber\n";
echo "\nYou can now view the generated certificate in: $outputPath\n";
?>
