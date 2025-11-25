<?php
require_once 'config.php';

$fileName = $_GET['file'] ?? '';
$appId = $_GET['app_id'] ?? '';

if (!$fileName || !$appId) {
    die("File not specified");
}

// Security check - ensure the file belongs to the user's application
if (isset($_SESSION['verified_phone'])) {
    $verifiedPhone = $_SESSION['verified_phone'];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT application_id FROM applications WHERE phone = ? AND application_id = ?");
        $stmt->execute([$verifiedPhone, $appId]);
        $application = $stmt->fetch();
        
        if (!$application) {
            die("Access denied");
        }
    } catch (Exception $e) {
        die("Error verifying access");
    }
}

$filePath = UPLOAD_DIR . $appId . '/' . $fileName;

if (!file_exists($filePath)) {
    die("File not found: " . htmlspecialchars($filePath));
}

// Get file info
$fileInfo = pathinfo($filePath);
$fileExtension = strtolower($fileInfo['extension']);

// Set appropriate headers
if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
    header('Content-Type: image/' . $fileExtension);
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
} elseif ($fileExtension === 'pdf') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
} else {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
}

header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>