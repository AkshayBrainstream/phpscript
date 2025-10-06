<?php
// Suppress all output before PDF generation
ob_start();
error_reporting(0);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Get the student row from POST request
if (!isset($_POST['student_row'])) {
    ob_end_clean();
    die('Error: Student row number is required');
}

$studentRow = intval($_POST['student_row']);

if ($studentRow < 3) {
    ob_end_clean();
    die('Error: Student row must be 3 or greater');
}

// Load the Excel file
$excelFile = 'GEPS SEM 5 and 6.xlsx';
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();

// Get data from Excel
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

// Check if student exists
if (empty($studentName)) {
    ob_end_clean();
    die('Error: No student found at row ' . $studentRow);
}

// Load the base image and generate the certificate
$imagePath = 'Format.jpeg';
$image = imagecreatefromjpeg($imagePath);

// Enable alpha blending for better text rendering
imagealphablending($image, true);
imagesavealpha($image, true);

// Define colors
$black = imagecolorallocate($image, 0, 0, 0);

// Define font
$fontPath = '/System/Library/Fonts/Helvetica.ttc';
$fontSize = 16;
$smallFontSize = 12;

// Add text to image
// Header fields
imagettftext($image, $fontSize, 0, 150, 115, $black, $fontPath, $studentName ?? '');
imagettftext($image, $fontSize, 0, 230, 150, $black, $fontPath, $enrollmentNumber ?? '');
imagettftext($image, $fontSize, 0, 440, 150, $black, $fontPath, $semester ?? '');
imagettftext($image, $fontSize, 0, 700, 150, $black, $fontPath, $branch ?? '');
imagettftext($image, $fontSize, 0, 200, 185, $black, $fontPath, '2024-25');
imagettftext($image, $fontSize, 0, 430, 185, $black, $fontPath, $cardIssueMonth ?? '');
imagettftext($image, $fontSize, 0, 700, 185, $black, $fontPath, $cardNumber ?? '');

// Academic scores
imagettftext($image, $smallFontSize, 0, 165, 272, $black, $fontPath, $spi ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 272, $black, $fontPath, $spi ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 290, $black, $fontPath, $sdp ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 290, $black, $fontPath, $sdp ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 325, $black, $fontPath, $researchPaper ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 325, $black, $fontPath, $researchPaper ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 343, $black, $fontPath, $patent ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 343, $black, $fontPath, $patent ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 361, $black, $fontPath, $bookPublished ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 361, $black, $fontPath, $bookPublished ?? '0');
imagettftext($image, $smallFontSize, 0, 190, 425, $black, $fontPath, $evaluatorMarks ?? '0');
imagettftext($image, $smallFontSize, 0, 190, 525, $black, $fontPath, $mentorMarks ?? '0');

// Save the image temporarily
$tempImagePath = 'temp_certificate_' . time() . '.jpg';
imagejpeg($image, $tempImagePath, 95);
imagedestroy($image);

// Clear any output buffer before creating PDF
ob_end_clean();

// Create PDF with TCPDF
$pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('GMIU Certificate Generator');
$pdf->SetAuthor('GMIU');
$pdf->SetTitle('Employability Performance Scale - ' . $studentName);
$pdf->SetSubject('Certificate');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Get image dimensions
$imageInfo = getimagesize($tempImagePath);
$imageWidth = $imageInfo[0];
$imageHeight = $imageInfo[1];

// Calculate dimensions to fit A4 landscape (297mm x 210mm)
$pageWidth = 297;
$pageHeight = 210;

// Calculate scaling to fit the page
$scaleWidth = $pageWidth / ($imageWidth / 25.4); // Convert pixels to mm
$scaleHeight = $pageHeight / ($imageHeight / 25.4);
$scale = min($scaleWidth, $scaleHeight) * 25.4;

// Center the image on the page
$x = ($pageWidth - ($imageWidth / $scale)) / 2;
$y = ($pageHeight - ($imageHeight / $scale)) / 2;

// Add the image to PDF
$pdf->Image($tempImagePath, 0, 0, $pageWidth, $pageHeight, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);

// Clean up temp image
unlink($tempImagePath);

// Output PDF
$pdfFileName = 'Certificate_' . str_replace(' ', '_', $studentName) . '_' . $enrollmentNumber . '.pdf';
$pdf->Output($pdfFileName, 'D'); // 'D' = force download

// Clean up
exit;
?>
