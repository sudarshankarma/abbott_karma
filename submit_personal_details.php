<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Check if phone is verified
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    // Validate required fields
    $requiredFields = ['fullName', 'email', 'phone'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field missing: " . $field);
        }
    }

    $phone = $_POST['phone'];
    
    // Verify that the phone matches the verified phone
    if ($phone !== $_SESSION['verified_phone']) {
        throw new Exception("Phone number mismatch");
    }

    $pdo = getDBConnection();
    
    // Check if application already exists for this phone
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE phone = ?");
    $checkStmt->execute([$phone]);
    $existingApp = $checkStmt->fetch();

    if ($existingApp) {
        // Update existing application
        $applicationId = $existingApp['application_id'];
        $sql = "UPDATE applications SET 
                full_name = :full_name,
                email = :email,
                whatsapp = :whatsapp,
                piramal_uan = :piramal_uan,
                abbott_uan = :abbott_uan,
                piramal_id = :piramal_id,
                abbott_id = :abbott_id,
                consent_given = :consent_given,
                phone_verified = :phone_verified,
                status = 'personal_details_completed',
                updated_at = NOW()
                WHERE application_id = :application_id";
    } else {
        // Create new application
        $applicationId = generateApplicationId();
        $sql = "INSERT INTO applications (
                application_id, full_name, email, phone, whatsapp, 
                piramal_uan, abbott_uan, piramal_id, abbott_id, 
                consent_given, phone_verified, status, created_at
            ) VALUES (
                :application_id, :full_name, :email, :phone, :whatsapp,
                :piramal_uan, :abbott_uan, :piramal_id, :abbott_id,
                :consent_given, :phone_verified, 'personal_details_completed', NOW()
            )";
    }

    $stmt = $pdo->prepare($sql);
    
    $params = [
        ':full_name' => $_POST['fullName'],
        ':email' => $_POST['email'],
        ':phone' => $phone,
        ':whatsapp' => $_POST['whatsapp'] ?? $phone,
        ':piramal_uan' => $_POST['piramalUAN'] ?? '',
        ':abbott_uan' => $_POST['abbottUAN'] ?? '',
        ':piramal_id' => $_POST['piramalId'] ?? '',
        ':abbott_id' => $_POST['abbottId'] ?? '',
        ':consent_given' => 1,
        ':phone_verified' => 1
    ];

    if ($existingApp) {
        $params[':application_id'] = $applicationId;
    } else {
        $params[':application_id'] = $applicationId;
    }

    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'applicationId' => $applicationId,
        'message' => 'Personal details saved successfully'
    ]);

} catch (Exception $e) {
    error_log("Personal details submission error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>