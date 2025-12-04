<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Check if user has given consent and phone is verified
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['application_id']) || empty($_POST['doc_type'])) {
        throw new Exception("Application ID and document type are required");
    }

    $applicationId = $_POST['application_id'];
    $docType = $_POST['doc_type'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Validate document type
    $validDocTypes = ['pan_card', 'aadhar_card', 'cancelled_cheque'];
    if (!in_array($docType, $validDocTypes)) {
        throw new Exception("Invalid document type");
    }

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Get the file path from applications table
    $stmt = $pdo->prepare("SELECT $docType FROM applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $result = $stmt->fetch();

    if (!$result || empty($result[$docType])) {
        throw new Exception("Document not found");
    }

    $fileName = $result[$docType];
    $filePath = UPLOAD_DIR . $applicationId . '/' . $fileName;

    if (!file_exists($filePath)) {
        // Try getting from document_uploads table as fallback
        $docStmt = $pdo->prepare("SELECT file_path FROM document_uploads WHERE application_id = ? AND document_type = ? ORDER BY uploaded_at DESC LIMIT 1");
        $docStmt->execute([$applicationId, $docType]);
        $docResult = $docStmt->fetch();
        
        if ($docResult && !empty($docResult['file_path']) && file_exists($docResult['file_path'])) {
            $filePath = $docResult['file_path'];
        } else {
            throw new Exception("Document file not found on server");
        }
    }

    echo json_encode([
        'success' => true,
        'file_path' => $filePath,
        'file_name' => basename($filePath),
        'file_size' => filesize($filePath),
        'file_type' => mime_content_type($filePath)
    ]);

} catch (Exception $e) {
    error_log("Get document info error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>