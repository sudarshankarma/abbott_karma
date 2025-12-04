<?php
require_once 'config.php';

try {
    // Check if user has given consent and phone is verified
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_GET['file']) || empty($_GET['type'])) {
        throw new Exception("File and type parameters are required");
    }

    $filePath = $_GET['file'];
    $docType = $_GET['type'];
    $previewMode = isset($_GET['preview']) && $_GET['preview'] == '1';

    // Security check - ensure file is within upload directory
    $realFilePath = realpath($filePath);
    $uploadDir = realpath(UPLOAD_DIR);
    
    if (strpos($realFilePath, $uploadDir) !== 0) {
        throw new Exception("Access denied");
    }

    // Verify the file exists
    if (!file_exists($realFilePath)) {
        throw new Exception("File not found");
    }

    // Get file info
    $fileName = basename($realFilePath);
    $fileSize = filesize($realFilePath);
    $fileType = mime_content_type($realFilePath);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Set headers based on preview or download mode
    if ($previewMode) {
        // For preview, use inline content-disposition
        header('Content-Type: ' . $fileType);
        header('Content-Disposition: inline; filename="' . $fileName . '"');
    } else {
        // For download, use attachment content-disposition
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
    
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Clear any output buffering
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Read the file
    readfile($realFilePath);
    exit;

} catch (Exception $e) {
    error_log("Download document error: " . $e->getMessage());
    
    // Show error page
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Document Error</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 50px; text-align: center; }
            .error { color: #dc3545; margin: 20px 0; }
            .btn { margin-top: 20px; }
        </style>
    </head>
    <body>
        <h1>Document Error</h1>
        <div class="error">' . htmlspecialchars($e->getMessage()) . '</div>
        <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
    </body>
    </html>';
    exit;
}
?>