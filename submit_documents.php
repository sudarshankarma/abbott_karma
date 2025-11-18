<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Check if phone is verified
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['application_id'])) {
        throw new Exception("Application ID is required");
    }

    $applicationId = $_POST['application_id'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_DIR . $applicationId . '/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Initialize variables
    $panCardPath = null;
    $aadharCardPath = null;
    $cancelledChequePath = null;
    $panNumber = $_POST['pan_number'] ?? null;
    $aadharNumber = $_POST['aadhar_number'] ?? null;

    // Handle file uploads - only process if file is actually uploaded
    if (isset($_FILES['pan_card']) && $_FILES['pan_card']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['pan_card'], $uploadDir, 'pan');
        if ($result['success']) {
            $panCardPath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type) 
                      VALUES (?, 'pan_card', ?, ?, ?, ?)";
            $docStmt = $pdo->prepare($docSql);
            $docStmt->execute([
                $applicationId,
                $result['filename'],
                $uploadDir . $result['filename'],
                $_FILES['pan_card']['size'],
                $_FILES['pan_card']['type']
            ]);
        } else {
            throw new Exception('PAN Card: ' . $result['error']);
        }
    }

    if (isset($_FILES['aadhar_card']) && $_FILES['aadhar_card']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['aadhar_card'], $uploadDir, 'aadhar');
        if ($result['success']) {
            $aadharCardPath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type) 
                      VALUES (?, 'aadhar_card', ?, ?, ?, ?)";
            $docStmt = $pdo->prepare($docSql);
            $docStmt->execute([
                $applicationId,
                $result['filename'],
                $uploadDir . $result['filename'],
                $_FILES['aadhar_card']['size'],
                $_FILES['aadhar_card']['type']
            ]);
        } else {
            throw new Exception('Aadhar Card: ' . $result['error']);
        }
    }

    if (isset($_FILES['cancelled_cheque']) && $_FILES['cancelled_cheque']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['cancelled_cheque'], $uploadDir, 'cheque');
        if ($result['success']) {
            $cancelledChequePath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type) 
                      VALUES (?, 'cancelled_cheque', ?, ?, ?, ?)";
            $docStmt = $pdo->prepare($docSql);
            $docStmt->execute([
                $applicationId,
                $result['filename'],
                $uploadDir . $result['filename'],
                $_FILES['cancelled_cheque']['size'],
                $_FILES['cancelled_cheque']['type']
            ]);
        } else {
            throw new Exception('Cancelled Cheque: ' . $result['error']);
        }
    }

    // Build dynamic update query based on what's provided
    $updateFields = [];
    $params = [];
    
    if ($panNumber !== null) {
        $updateFields[] = "pan_number = ?";
        $params[] = $panNumber;
    }
    
    if ($aadharNumber !== null) {
        $updateFields[] = "aadhar_number = ?";
        $params[] = $aadharNumber;
    }
    
    if ($panCardPath !== null) {
        $updateFields[] = "pan_card = ?";
        $params[] = $panCardPath;
    }
    
    if ($aadharCardPath !== null) {
        $updateFields[] = "aadhar_card = ?";
        $params[] = $aadharCardPath;
    }
    
    if ($cancelledChequePath !== null) {
        $updateFields[] = "cancelled_cheque = ?";
        $params[] = $cancelledChequePath;
    }
    
    // Only update status if at least one document was uploaded
    if ($panCardPath !== null || $aadharCardPath !== null || $cancelledChequePath !== null) {
        $updateFields[] = "status = ?";
        $params[] = 'documents_uploaded';
    }
    
    $updateFields[] = "updated_at = NOW()";
    
    // Add application_id to params
    $params[] = $applicationId;

    if (!empty($updateFields)) {
        $sql = "UPDATE applications SET " . implode(', ', $updateFields) . " WHERE application_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Documents processed successfully',
        'documents_uploaded' => [
            'pan_card' => $panCardPath !== null,
            'aadhar_card' => $aadharCardPath !== null,
            'cancelled_cheque' => $cancelledChequePath !== null
        ]
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