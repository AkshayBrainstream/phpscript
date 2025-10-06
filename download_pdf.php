<?php
session_start();

// Check if we have the PDF info
if (!isset($_SESSION['generated_pdf_path']) || !file_exists($_SESSION['generated_pdf_path'])) {
    header('Location: index.php');
    exit;
}

$pdfFilePath = $_SESSION['generated_pdf_path'];
$pdfFileName = $_SESSION['generated_pdf'];

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
header('Content-Length: ' . filesize($pdfFilePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the file
readfile($pdfFilePath);

// Don't delete the file immediately - allow re-download
exit;
?>
