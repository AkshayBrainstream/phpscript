<?php
session_start();

// Suppress all output before PDF generation
ob_start();
error_reporting(0);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if we have the Excel file in session
if (!isset($_SESSION['excel_file']) || !file_exists($_SESSION['excel_file'])) {
    ob_end_clean();
    die('Error: No Excel file found. Please upload a file first.');
}

// Check if we have students data
if (!isset($_SESSION['students']) || empty($_SESSION['students'])) {
    ob_end_clean();
    die('Error: No student data found. Please upload a file first.');
}

$students = $_SESSION['students'];
$excelFile = $_SESSION['excel_file'];

// Load the Excel file
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();

// Load the base certificate image
$baseImagePath = 'Format.jpeg';
if (!file_exists($baseImagePath)) {
    ob_end_clean();
    die('Error: Certificate template (Format.jpeg) not found.');
}

// Clear any output buffer before creating PDF
ob_end_clean();

// Create PDF with TCPDF
$pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('GMIU Certificate Generator');
$pdf->SetAuthor('GMIU');
$pdf->SetTitle('Employability Performance Scale Certificates');
$pdf->SetSubject('Certificates');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins to 0 for full-page image
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);

// Define font for GD image processing
$fontPath = '/System/Library/Fonts/Helvetica.ttc';
$fontSize = 16;
$smallFontSize = 12;

// Define colors for GD
$tempImages = [];

// Generate a certificate for each student
foreach ($students as $index => $student) {
    $studentRow = $student['row'];

    // Get data from student array
    $studentName = $student['name'];
    $enrollmentNumber = $student['enrollment'];
    $semester = $student['semester'];
    $branch = $student['branch'];
    $cardIssueMonth = $student['card_issue_month'];
    $cardNumber = $student['card_number'];
    $spi = $student['spi'];
    $sdp = $student['sdp'];
    $researchPaper = $student['research_paper'];
    $bookPublished = $student['book_published'];
    $patent = $student['patent'];
    $evaluatorMarks = $student['evaluator_marks'];
    $mentorMarks = $student['mentor_marks'];

    // Create image from template
    $image = imagecreatefromjpeg($baseImagePath);

    // Enable alpha blending for better text rendering
    imagealphablending($image, true);
    imagesavealpha($image, true);

    // Define colors
    $black = imagecolorallocate($image, 0, 0, 0);

    // Add text to image - Header fields
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
    $tempImagePath = 'temp_certificate_' . $index . '_' . time() . '.jpg';
    imagejpeg($image, $tempImagePath, 95);
    imagedestroy($image);

    // Store temp image path for cleanup
    $tempImages[] = $tempImagePath;

    // Add a new page to PDF
    $pdf->AddPage();

    // Get page dimensions
    $pageWidth = 297; // A4 landscape width in mm
    $pageHeight = 210; // A4 landscape height in mm

    // Add the image to PDF (full page)
    $pdf->Image($tempImagePath, 0, 0, $pageWidth, $pageHeight, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);
}

// Clean up all temp images
foreach ($tempImages as $tempImage) {
    if (file_exists($tempImage)) {
        unlink($tempImage);
    }
}

// Output PDF as download
$pdfFileName = 'GMIU_Certificates_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($pdfFileName, 'D'); // 'D' = force download

// Clean up
exit;
?>
