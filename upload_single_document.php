<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['application_id']) || empty($_POST['document_type'])) {
        throw new Exception("Application ID and document type are required");
    }

    $applicationId = $_POST['application_id'];
    $documentType = $_POST['document_type'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Handle file upload
    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Please select a valid file");
    }

    $uploadDir = UPLOAD_DIR . $applicationId . '/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    $result = uploadFile($_FILES['document_file'], $uploadDir, $documentType);
    if (!$result['success']) {
        throw new Exception($result['error']);
    }

    // Build update query based on document type
    $sql = "UPDATE applications SET ";
    $params = [];
    
    switch($documentType) {
        case 'pan_card':
            $sql .= "pan_card = ?, pan_number = ?, pan_status = 'pending'";
            $params[] = $result['filename'];
            $params[] = $_POST['document_number'] ?? '';
            break;
            
        case 'aadhar_card':
            $sql .= "aadhar_card = ?, aadhar_number = ?, aadhar_status = 'pending'";
            $params[] = $result['filename'];
            $params[] = $_POST['document_number'] ?? '';
            break;
            
        case 'cancelled_cheque':
            $sql .= "cancelled_cheque = ?, cheque_status = 'pending'";
            $params[] = $result['filename'];
            break;
            
        case 'acknowledge_doc':
            $sql .= "acknowledge_doc = ?";
            $params[] = $result['filename'];
            break;
            
        default:
            throw new Exception("Invalid document type");
    }
    
    $sql .= ", admin_status = 'under_review', updated_at = NOW() WHERE application_id = ?";
    $params[] = $applicationId;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'message' => 'Document uploaded successfully'
    ]);

} catch (Exception $e) {
    error_log("Document upload error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>