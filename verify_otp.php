<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Check if consent is given and phone is set
    if (!isset($_SESSION['consent_given']) || $_SESSION['consent_given'] !== true) {
        throw new Exception("Consent required");
    }

    if (empty($_POST['phone']) || empty($_POST['otp'])) {
        throw new Exception("Phone number and OTP are required");
    }

    $phone = $_POST['phone'];
    $otp = $_POST['otp'];

    // Verify OTP
    if (!verifyOTP($phone, $otp)) {
        throw new Exception("Invalid OTP or OTP expired");
    }

    // OTP verified successfully
    $_SESSION['verified_phone'] = $phone;
    
    // Check if application exists and get status
    $application = getApplicationStatus($phone);
    
    if ($application) {
        // Application exists - mark phone as verified
        $pdo = getDBConnection();
        $updateSql = "UPDATE applications SET phone_verified = 1 WHERE phone = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$phone]);
        
        // Return application status to redirect appropriately
        echo json_encode([
            'success' => true,
            'application_exists' => true,
            'status' => $application['status'],
            'application_id' => $application['application_id'],
            'message' => 'OTP verified successfully'
        ]);
    } else {
        // New application
        echo json_encode([
            'success' => true,
            'application_exists' => false,
            'message' => 'OTP verified successfully'
        ]);
    }

} catch (Exception $e) {
    error_log("OTP verification error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>