<?php
require_once 'config.php';

header('Content-Type: application/json');

function uploadFile($file, $uploadDir, $prefix, $applicationId, $pdo, $documentType) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }

    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10MB limit');
    }

    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only PDF, JPG, and PNG allowed');
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file');
    }

    return ['success' => true, 'filename' => $filename];
}

function deleteOldFile($pdo, $applicationId, $documentType, $uploadDir) {
    try {
        // Get the current file name from applications table
        $stmt = $pdo->prepare("SELECT $documentType FROM applications WHERE application_id = ?");
        $stmt->execute([$applicationId]);
        $result = $stmt->fetch();
        
        if ($result && !empty($result[$documentType])) {
            $oldFile = $uploadDir . $result[$documentType];
            if (file_exists($oldFile)) {
                // Delete the old file
                if (unlink($oldFile)) {
                    error_log("Deleted old file: " . $oldFile);
                } else {
                    error_log("Failed to delete old file: " . $oldFile);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error deleting old file: " . $e->getMessage());
        // Don't throw exception, just log error
    }
}

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
    $isCompleteSubmission = isset($_POST['complete_application']);
    $isSaveProgress = isset($_POST['save_progress']);

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Don't allow changes if application is already completed and it's a save progress
    if ($application['status'] === 'completed' && $isSaveProgress) {
        echo json_encode([
            'success' => true,
            'message' => 'Application is already completed and under review.',
            'is_complete' => true
        ]);
        exit;
    }

    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_DIR . $applicationId . '/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory. Please check folder permissions.');
        }
    }

    // Ensure upload directory is writable
    if (!is_writable($uploadDir)) {
        throw new Exception('Upload directory is not writable. Please check folder permissions.');
    }

    // Initialize variables
    $panCardPath = null;
    $aadharCardPath = null;
    $cancelledChequePath = null;
    $panNumber = isset($_POST['pan_number']) ? trim($_POST['pan_number']) : null;
    $aadharNumber = isset($_POST['aadhar_number']) ? trim($_POST['aadhar_number']) : null;

    // Handle file uploads - only process if file is actually uploaded
    if (isset($_FILES['pan_card']) && $_FILES['pan_card']['error'] === UPLOAD_ERR_OK) {
        // Delete old PAN card file if exists
        deleteOldFile($pdo, $applicationId, 'pan_card', $uploadDir);
        
        $result = uploadFile($_FILES['pan_card'], $uploadDir, 'pan', $applicationId, $pdo, 'pan_card');
        if ($result['success']) {
            $panCardPath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type, uploaded_at) 
                      VALUES (?, 'pan_card', ?, ?, ?, ?, NOW())";
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
        // Delete old Aadhar card file if exists
        deleteOldFile($pdo, $applicationId, 'aadhar_card', $uploadDir);
        
        $result = uploadFile($_FILES['aadhar_card'], $uploadDir, 'aadhar', $applicationId, $pdo, 'aadhar_card');
        if ($result['success']) {
            $aadharCardPath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type, uploaded_at) 
                      VALUES (?, 'aadhar_card', ?, ?, ?, ?, NOW())";
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
        // Delete old cancelled cheque file if exists
        deleteOldFile($pdo, $applicationId, 'cancelled_cheque', $uploadDir);
        
        $result = uploadFile($_FILES['cancelled_cheque'], $uploadDir, 'cheque', $applicationId, $pdo, 'cancelled_cheque');
        if ($result['success']) {
            $cancelledChequePath = $result['filename'];
            
            // Also save to document_uploads table
            $docSql = "INSERT INTO document_uploads (application_id, document_type, file_name, file_path, file_size, file_type, uploaded_at) 
                      VALUES (?, 'cancelled_cheque', ?, ?, ?, ?, NOW())";
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
    
    if ($panNumber !== null && $panNumber !== '') {
        $updateFields[] = "pan_number = ?";
        $params[] = $panNumber;
    }
    
    if ($aadharNumber !== null && $aadharNumber !== '') {
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
    
    // Determine status based on submission type
    if ($isCompleteSubmission) {
        // Check if all three documents are present (either already in DB or newly uploaded)
        $checkDocs = $pdo->prepare("SELECT pan_card, aadhar_card, cancelled_cheque, pan_number, aadhar_number FROM applications WHERE application_id = ?");
        $checkDocs->execute([$applicationId]);
        $currentDocs = $checkDocs->fetch();
        
        // Determine what we have after updates
        $finalPanCard = !empty($currentDocs['pan_card']) || $panCardPath !== null;
        $finalAadharCard = !empty($currentDocs['aadhar_card']) || $aadharCardPath !== null;
        $finalCancelledCheque = !empty($currentDocs['cancelled_cheque']) || $cancelledChequePath !== null;
        $finalPanNumber = !empty($panNumber) ? true : !empty($currentDocs['pan_number']);
        $finalAadharNumber = !empty($aadharNumber) ? true : !empty($currentDocs['aadhar_number']);
        
        // For final submission, ALL documents and numbers are required
        if (!$finalPanCard || !$finalAadharCard || !$finalCancelledCheque || !$finalPanNumber || !$finalAadharNumber) {
            $missing = [];
            if (!$finalPanCard) $missing[] = 'PAN Card';
            if (!$finalAadharCard) $missing[] = 'Aadhar Card';
            if (!$finalCancelledCheque) $missing[] = 'Cancelled Cheque';
            if (!$finalPanNumber) $missing[] = 'PAN Number';
            if (!$finalAadharNumber) $missing[] = 'Aadhar Number';
            
            throw new Exception('Cannot complete application. Missing: ' . implode(', ', $missing));
        }
        
        // All requirements met, mark as completed
        $updateFields[] = "status = ?";
        $params[] = 'completed';
        $updateFields[] = "admin_status = ?";
        $params[] = 'under_review';
        $updateFields[] = "submitted_at = NOW()";
    } else if ($isSaveProgress) {
        // For save progress, just update what was provided
        // Check if we have any documents (either in DB or current upload)
        $hasAnyDoc = !empty($application['pan_card']) || !empty($application['aadhar_card']) || !empty($application['cancelled_cheque']) ||
                     $panCardPath !== null || $aadharCardPath !== null || $cancelledChequePath !== null;
        
        if ($hasAnyDoc && $application['status'] !== 'completed') {
            $updateFields[] = "status = ?";
            $params[] = 'documents_uploaded';
        }
    }
    
    $updateFields[] = "updated_at = NOW()";
    
    // Add application_id to params
    $params[] = $applicationId;

    if (!empty($updateFields)) {
        $sql = "UPDATE applications SET " . implode(', ', $updateFields) . " WHERE application_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    // Prepare response
    $response = [
        'success' => true,
        'message' => $isCompleteSubmission ? 'Application submitted successfully!' : 'Progress saved successfully',
        'documents_uploaded' => [
            'pan_card' => $panCardPath !== null,
            'aadhar_card' => $aadharCardPath !== null,
            'cancelled_cheque' => $cancelledChequePath !== null
        ],
        'is_complete' => $isCompleteSubmission
    ];

    // If it's a save progress, also return current upload status
    if ($isSaveProgress) {
        $statusStmt = $pdo->prepare("SELECT pan_card, aadhar_card, cancelled_cheque FROM applications WHERE application_id = ?");
        $statusStmt->execute([$applicationId]);
        $currentStatus = $statusStmt->fetch();
        
        $response['current_status'] = [
            'has_pan' => !empty($currentStatus['pan_card']),
            'has_aadhar' => !empty($currentStatus['aadhar_card']),
            'has_cheque' => !empty($currentStatus['cancelled_cheque'])
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Document upload error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>