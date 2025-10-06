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

// Get student index from URL
if (!isset($_GET['index']) || !is_numeric($_GET['index'])) {
    ob_end_clean();
    die('Error: Invalid student index.');
}

$studentIndex = (int)$_GET['index'];
$students = $_SESSION['students'];

// Validate index
if ($studentIndex < 0 || $studentIndex >= count($students)) {
    ob_end_clean();
    die('Error: Student not found.');
}

$student = $students[$studentIndex];
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
$pdf->SetTitle('Employability Performance Scale Certificate - ' . $student['name']);
$pdf->SetSubject('Certificate');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins to 0 for full-page image
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);

// Define font for GD image processing
$fontSize = 16;
$smallFontSize = 12;

// Use bundled font from assets folder (cross-platform solution)
$fontPath = 'assets/fonts/arial.ttf';

// Check if font file exists
if (!file_exists($fontPath)) {
    ob_end_clean();
    die('Error: Font file not found at ' . $fontPath . '. Please place arial.ttf in the assets/fonts directory.');
}

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
$softSkillMarks = $student['soft_skill_marks'];
$labTestingSkill = $student['lab_testing_skill'];
$laboratoryTestingSkill = $student['laboratory_testing_skill'];
$domainBasedPracticalSkill = $student['domain_based_practical_skill'];
$industrialVisit = $student['industrial_visit'];
$industrialTraining = $student['industrial_training'];
$workshopAttended = $student['workshop_attended'];
$seminarExpertTalk = $student['seminar_expert_talk'];
$softwareSkills = $student['software_skills'];
$verbalAspect = $student['verbal_aspect'];
$majorProjectType = $student['major_project_type'];
$paMembership = $student['pa_membership'];
$professionalMembership = $student['professional_membership'];
$technoArtCompetition = $student['techno_art_competition'];
$conferenceAttended = $student['conference_attended'];
$onlineCourse = $student['online_course'];
$linkageProfile = $student['linkage_profile'];
$freelanceProject = $student['freelance_project'];
$suraProfile = $student['sura_profile'];
$resumeUpdation = $student['resume_updation'];
$iqTest = $student['iq_test'];
$edTest = $student['ed_test'];
$aptitudeTest = $student['aptitude_test'];

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

// Academic scores (Left section - Academic)
imagettftext($image, $smallFontSize, 0, 165, 272, $black, $fontPath, $spi ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 272, $black, $fontPath, $spi ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 290, $black, $fontPath, $sdp ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 290, $black, $fontPath, $sdp ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 308, $black, $fontPath, '0'); // TSEP
imagettftext($image, $smallFontSize, 0, 210, 308, $black, $fontPath, '0'); // TSEP Overall
imagettftext($image, $smallFontSize, 0, 165, 325, $black, $fontPath, $researchPaper ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 325, $black, $fontPath, $researchPaper ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 343, $black, $fontPath, $patent ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 343, $black, $fontPath, $patent ?? '0');
imagettftext($image, $smallFontSize, 0, 165, 361, $black, $fontPath, $bookPublished ?? '0');
imagettftext($image, $smallFontSize, 0, 210, 361, $black, $fontPath, $bookPublished ?? '0');

// Evaluator and Mentor marks
imagettftext($image, $smallFontSize, 0, 190, 425, $black, $fontPath, $evaluatorMarks ?? '0');
imagettftext($image, $smallFontSize, 0, 190, 525, $black, $fontPath, $mentorMarks ?? '0');

// Middle section - Placement Activity
imagettftext($image, $smallFontSize, 0, 425, 280, $black, $fontPath, $laboratoryTestingSkill ?? '0'); // Lab Testing Sem
imagettftext($image, $smallFontSize, 0, 475, 280, $black, $fontPath, $laboratoryTestingSkill ?? '0'); // Lab Testing Overall
imagettftext($image, $smallFontSize, 0, 425, 298, $black, $fontPath, $domainBasedPracticalSkill ?? '0'); // Equipment Operation Sem
imagettftext($image, $smallFontSize, 0, 475, 298, $black, $fontPath, $domainBasedPracticalSkill ?? '0'); // Equipment Operation Overall
imagettftext($image, $smallFontSize, 0, 425, 316, $black, $fontPath, $industrialVisit ?? '0'); // Industrial Visit Sem
imagettftext($image, $smallFontSize, 0, 475, 316, $black, $fontPath, $industrialVisit ?? '0'); // Industrial Visit Overall
imagettftext($image, $smallFontSize, 0, 425, 334, $black, $fontPath, $industrialTraining ?? '0'); // Industrial Training Sem
imagettftext($image, $smallFontSize, 0, 475, 334, $black, $fontPath, $industrialTraining ?? '0'); // Industrial Training Overall
imagettftext($image, $smallFontSize, 0, 425, 352, $black, $fontPath, $workshopAttended ?? '0'); // Workshop Sem
imagettftext($image, $smallFontSize, 0, 475, 352, $black, $fontPath, $workshopAttended ?? '0'); // Workshop Overall
imagettftext($image, $smallFontSize, 0, 425, 370, $black, $fontPath, $seminarExpertTalk ?? '0'); // Seminar Sem
imagettftext($image, $smallFontSize, 0, 475, 370, $black, $fontPath, $seminarExpertTalk ?? '0'); // Seminar Overall
imagettftext($image, $smallFontSize, 0, 425, 388, $black, $fontPath, $softwareSkills ?? '0'); // Software Skills Sem
imagettftext($image, $smallFontSize, 0, 475, 388, $black, $fontPath, $softwareSkills ?? '0'); // Software Skills Overall
imagettftext($image, $smallFontSize, 0, 425, 406, $black, $fontPath, '0'); // Minor Project Sem
imagettftext($image, $smallFontSize, 0, 475, 406, $black, $fontPath, '0'); // Minor Project Overall
imagettftext($image, $smallFontSize, 0, 425, 424, $black, $fontPath, $majorProjectType ?? '0'); // Major Project Sem
imagettftext($image, $smallFontSize, 0, 475, 424, $black, $fontPath, $majorProjectType ?? '0'); // Major Project Overall
imagettftext($image, $smallFontSize, 0, 425, 442, $black, $fontPath, $paMembership ?? '0'); // PA Attendance Sem
imagettftext($image, $smallFontSize, 0, 475, 442, $black, $fontPath, $paMembership ?? '0'); // PA Attendance Overall
imagettftext($image, $smallFontSize, 0, 475, 460, $black, $fontPath, $professionalMembership ?? '0'); // Professional Membership

// Right section - Technical Event Participation/Online Courses
imagettftext($image, $smallFontSize, 0, 720, 280, $black, $fontPath, $technoArtCompetition ?? '0'); // Technical Event Sem
imagettftext($image, $smallFontSize, 0, 770, 280, $black, $fontPath, $technoArtCompetition ?? '0'); // Technical Event Overall
imagettftext($image, $smallFontSize, 0, 720, 298, $black, $fontPath, $conferenceAttended ?? '0'); // Conference Sem
imagettftext($image, $smallFontSize, 0, 770, 298, $black, $fontPath, $conferenceAttended ?? '0'); // Conference Overall
imagettftext($image, $smallFontSize, 0, 720, 316, $black, $fontPath, $onlineCourse ?? '0'); // Online Courses Sem
imagettftext($image, $smallFontSize, 0, 770, 316, $black, $fontPath, $onlineCourse ?? '0'); // Online Courses Overall

// Right section - Co & Extra Curricular Activities
imagettftext($image, $smallFontSize, 0, 770, 405, $black, $fontPath, $linkageProfile ?? '0'); // LinkedIn Profile
imagettftext($image, $smallFontSize, 0, 770, 423, $black, $fontPath, $freelanceProject ?? '0'); // Freelancer Profile
imagettftext($image, $smallFontSize, 0, 770, 441, $black, $fontPath, '0'); // SILP Course
imagettftext($image, $smallFontSize, 0, 770, 459, $black, $fontPath, $resumeUpdation ?? '0'); // Resume Updated
imagettftext($image, $smallFontSize, 0, 770, 477, $black, $fontPath, $iqTest ?? '0'); // IQ Test
imagettftext($image, $smallFontSize, 0, 770, 495, $black, $fontPath, $edTest ?? '0'); // EQ Test
imagettftext($image, $smallFontSize, 0, 770, 513, $black, $fontPath, $aptitudeTest ?? '0'); // Aptitude Test

// TPA Cell - Most Recent HR, Communication Skill & Management Skill
imagettftext($image, $smallFontSize, 0, 520, 595, $black, $fontPath, $softSkillMarks ?? '0'); // TPA Cell Overall

// Save the image temporarily
$tempImagePath = 'temp_certificate_' . $studentIndex . '_' . time() . '.jpg';
imagejpeg($image, $tempImagePath, 95);
imagedestroy($image);

// Add a new page to PDF
$pdf->AddPage();

// Get page dimensions
$pageWidth = 297; // A4 landscape width in mm
$pageHeight = 210; // A4 landscape height in mm

// Add the image to PDF (full page)
$pdf->Image($tempImagePath, 0, 0, $pageWidth, $pageHeight, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);

// Clean up temp image
if (file_exists($tempImagePath)) {
    unlink($tempImagePath);
}

// Output PDF as download
$pdfFileName = 'GMIU_Certificate_' . preg_replace('/[^a-zA-Z0-9]/', '_', $studentName) . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($pdfFileName, 'D'); // 'D' = force download

// Clean up
exit;
?>
