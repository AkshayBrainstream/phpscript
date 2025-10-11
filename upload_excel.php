<?php
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if we already have students in session (pagination navigation)
if (isset($_SESSION['students']) && !isset($_FILES['excel_file'])) {
    // Just viewing existing data with pagination
    $students = $_SESSION['students'];

    // Pagination variables
    $perPage = 10;
    $totalStudents = count($students);
    $totalPages = ceil($totalStudents / $perPage);
    $currentPage = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
    $offset = ($currentPage - 1) * $perPage;

    // Skip to display section
    goto display_students;
}

// Check if file was uploaded
if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?error=upload');
    exit;
}

// Create uploads directory if it doesn't exist
$uploadsDir = 'uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// Get file info
$file = $_FILES['excel_file'];
$fileName = basename($file['name']);
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($fileExt, ['xlsx', 'xls'])) {
    header('Location: index.php?error=invalid_file');
    exit;
}

// Generate unique filename and move uploaded file
$newFileName = 'uploaded_' . time() . '.' . $fileExt;
$filePath = $uploadsDir . '/' . $newFileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    header('Location: index.php?error=move_failed');
    exit;
}

// Store the file path in session
$_SESSION['excel_file'] = $filePath;

try {
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();

    // Get the highest row number
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();

    // Extract all data (starting from row 3 as per your structure)
    $students = [];
    for ($row = 3; $row <= $highestRow; $row++) {
        $studentName = $worksheet->getCell('A' . $row)->getValue();

        // Skip empty rows
        if (empty($studentName)) {
            continue;
        }

        $students[] = [
            'row' => $row,
            'name' => $studentName,
            'enrollment' => $worksheet->getCell('B' . $row)->getValue(),
            'semester' => $worksheet->getCell('C' . $row)->getValue(),
            'branch' => $worksheet->getCell('D' . $row)->getValue(),
            'card_issue_month' => $worksheet->getCell('E' . $row)->getValue(),
            'card_number' => $worksheet->getCell('F' . $row)->getValue(),
            'spi' => $worksheet->getCell('G' . $row)->getValue(),
            'sdp' => $worksheet->getCell('H' . $row)->getValue(),
            'research_paper' => $worksheet->getCell('I' . $row)->getValue(),
            'book_published' => $worksheet->getCell('J' . $row)->getValue(),
            'patent' => $worksheet->getCell('K' . $row)->getValue(),
            'evaluator_marks' => $worksheet->getCell('L' . $row)->getValue(),
            'mentor_marks' => $worksheet->getCell('M' . $row)->getValue(),
            'soft_skill_marks' => $worksheet->getCell('N' . $row)->getValue(),
            'lab_testing_skill' => $worksheet->getCell('O' . $row)->getValue(),
            'laboratory_testing_skill' => $worksheet->getCell('P' . $row)->getValue(),
            'domain_based_practical_skill' => $worksheet->getCell('Q' . $row)->getValue(),
            'industrial_visit' => $worksheet->getCell('R' . $row)->getValue(),
            'industrial_training' => $worksheet->getCell('S' . $row)->getValue(),
            'workshop_attended' => $worksheet->getCell('T' . $row)->getValue(),
            'seminar_expert_talk' => $worksheet->getCell('U' . $row)->getValue(),
            'software_skills' => $worksheet->getCell('V' . $row)->getValue(),
            'verbal_aspect' => $worksheet->getCell('W' . $row)->getValue(),
            'major_project_type' => $worksheet->getCell('X' . $row)->getValue(),
            'pa_membership' => $worksheet->getCell('Y' . $row)->getValue(),
            'professional_membership' => $worksheet->getCell('Z' . $row)->getValue(),
            'techno_art_competition' => $worksheet->getCell('AA' . $row)->getValue(),
            'conference_attended' => $worksheet->getCell('AB' . $row)->getValue(),
            'online_course' => $worksheet->getCell('AC' . $row)->getValue(),
            'linkage_profile' => $worksheet->getCell('AD' . $row)->getValue(),
            'freelance_project' => $worksheet->getCell('AE' . $row)->getValue(),
            'sura_profile' => $worksheet->getCell('AF' . $row)->getValue(),
            'resume_updation' => $worksheet->getCell('AG' . $row)->getValue(),
            'iq_test' => $worksheet->getCell('AH' . $row)->getValue(),
            'ed_test' => $worksheet->getCell('AI' . $row)->getValue(),
            'aptitude_test' => $worksheet->getCell('AJ' . $row)->getValue(),
        ];
    }

    // Store students data in session
    $_SESSION['students'] = $students;

    // Pagination variables
    $perPage = 10;
    $totalStudents = count($students);
    $totalPages = ceil($totalStudents / $perPage);
    $currentPage = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
    $offset = ($currentPage - 1) * $perPage;

} catch (Exception $e) {
    header('Location: index.php?error=read_failed&msg=' . urlencode($e->getMessage()));
    exit;
}

display_students:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records - GMIU Certificate Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 1400px;
            width: 100%;
            margin: 20px auto;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .info-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #155724;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .btn-success:hover {
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        .loading.active {
            display: block;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn-download {
            padding: 6px 12px;
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .page-link {
            padding: 8px 12px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .page-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        .page-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            cursor: default;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gyanmanjari Innovative University</h1>
        <p class="subtitle">Review the uploaded students data and generate certificates</p>

        <div class="info-box">
            <strong>File Uploaded Successfully!</strong><br>
            Found <?php echo $totalStudents; ?> student records. Showing <?php echo min($perPage, $totalStudents - $offset); ?> records (Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>).
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Enrollment</th>
                        <th>Semester</th>
                        <th>Branch</th>
                        <th>Card Month</th>
                        <th>Card No.</th>
                        <th>SPI</th>
                        <th>SDP</th>
                        <th>Research</th>
                        <th>Book</th>
                        <th>Patent</th>
                        <th>Eval. Marks</th>
                        <th>Mentor Marks</th>
                        <th>Soft Skills</th>
                        <th>Lab Testing</th>
                        <th>Laboratory Testing</th>
                        <th>Domain Practical</th>
                        <th>Industrial Visit</th>
                        <th>Industrial Training</th>
                        <th>Workshop</th>
                        <th>Seminar/Expert</th>
                        <th>Software Skills</th>
                        <th>Verbal Aspect</th>
                        <th>Major Project</th>
                        <th>PA Membership</th>
                        <th>Professional</th>
                        <th>Techno Art</th>
                        <th>Conference</th>
                        <th>Online Course</th>
                        <th>Linkage Profile</th>
                        <th>Freelance</th>
                        <th>SURA Profile</th>
                        <th>Resume</th>
                        <th>IQ Test</th>
                        <th>ED Test</th>
                        <th>Aptitude</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $studentsToShow = array_slice($students, $offset, $perPage, true);
                    foreach ($studentsToShow as $index => $student):
                    ?>
                    <tr>
                        <td>
                            <a href="generate_single_certificate.php?index=<?php echo $index; ?>" class="btn-download" title="Download Certificate" target="_black">
                                📄 Download
                            </a>
                        </td>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($student['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['enrollment'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['semester'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['branch'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['card_issue_month'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['card_number'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['spi'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['sdp'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['research_paper'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['book_published'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['patent'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['evaluator_marks'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['mentor_marks'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['soft_skill_marks'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['lab_testing_skill'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['laboratory_testing_skill'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['domain_based_practical_skill'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['industrial_visit'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['industrial_training'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['workshop_attended'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['seminar_expert_talk'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['software_skills'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['verbal_aspect'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['major_project_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['pa_membership'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['professional_membership'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['techno_art_competition'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['conference_attended'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['online_course'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['linkage_profile'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['freelance_project'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['sura_profile'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['resume_updation'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['iq_test'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['ed_test'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['aptitude_test'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>" class="page-link">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="page-link active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>" class="page-link">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="index.php" class="btn btn-secondary">Upload New File</a>
            <!-- <form action="generate_all_certificates.php" method="POST" style="display: inline;" id="generateForm">
                <button type="submit" class="btn btn-success" id="generateBtn">Generate All Certificates</button>
            </form> -->
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 15px; color: #667eea; font-weight: 600;">Generating certificates... Please wait.</p>
        </div>
    </div>

    <script>
        document.getElementById('generateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('generateBtn');
            const loading = document.getElementById('loading');

            // Show loading
            btn.disabled = true;
            loading.classList.add('active');

            // Create a hidden iframe for the download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = 'generate_all_certificates.php';
            document.body.appendChild(iframe);

            // Stop loader after 3 seconds (adjust based on your needs)
            setTimeout(function() {
                loading.classList.remove('active');
                btn.disabled = false;
                btn.textContent = 'Download Started!';

                // Reset button after 2 seconds
                setTimeout(function() {
                    btn.textContent = 'Generate All Certificates';
                }, 2000);
            }, 3000);
        });
    </script>
</body>
</html>
