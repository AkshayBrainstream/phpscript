<?php
session_start();

// Check if we have the PDF info
if (!isset($_SESSION['generated_pdf']) || !isset($_SESSION['generated_pdf_path'])) {
    header('Location: index.php');
    exit;
}

$pdfFileName = $_SESSION['generated_pdf'];
$pdfFilePath = $_SESSION['generated_pdf_path'];
$totalCertificates = $_SESSION['total_certificates'] ?? 0;

// Verify file exists
if (!file_exists($pdfFilePath)) {
    die('Error: Generated PDF not found.');
}

$fileSize = filesize($pdfFilePath);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates Generated - GMIU</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon::after {
            content: '✓';
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #333;
        }
        .info-value {
            color: #667eea;
            font-weight: 600;
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
            margin: 5px;
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
        .actions {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon"></div>
        <h1>Certificates Generated Successfully!</h1>
        <p class="subtitle">Your PDF has been created and is ready to download</p>

        <div class="info-box">
            <div class="info-item">
                <span class="info-label">Total Certificates:</span>
                <span class="info-value"><?php echo $totalCertificates; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">File Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($pdfFileName); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">File Size:</span>
                <span class="info-value"><?php echo $fileSizeMB; ?> MB</span>
            </div>
        </div>

        <div class="actions">
            <a href="download_pdf.php" class="btn btn-success">Download PDF</a>
            <a href="index.php" class="btn btn-secondary">Upload New File</a>
        </div>
    </div>

    <script>
        // Auto-download the PDF after 1 second
        setTimeout(function() {
            window.location.href = 'download_pdf.php';
        }, 1000);
    </script>
</body>
</html>
