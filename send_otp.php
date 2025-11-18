<?php
require_once 'config.php';

header('Content-Type: application/json');

define('SMS_API_KEY', 'omUOKglyDEG8fl6lSFSE6w');
define('SMS_SENDER_ID', 'SMSHUB');

function sendSMS($phone, $otp) {
    try {
        $formatted_number = '91' . $phone;
        
        // Use the exact message format from your working example
        $message = "Welcome to the Karma. Your OTP for registration is " . $otp;
        
        // Build the direct URL as per your working example
        $api_url = "https://cloud.smsindiahub.in/vendorsms/pushsms.aspx?" . 
                   "APIKey=" . SMS_API_KEY . 
                   "&msisdn=" . $formatted_number . 
                   "&sid=" . SMS_SENDER_ID . 
                   "&msg=" . urlencode($message) . 
                   "&fl=0&gwid=2";
        
        // Directly call the URL
        $response = file_get_contents($api_url);
        
        if ($response === FALSE) {
            error_log("SMS API request failed for phone: $phone");
            return false;
        }
        
        // Log the full response for debugging
        error_log("SMS API Response for $phone: " . $response);
        
        // Check if response contains success indicator
        if (stripos($response, 'success') !== false || stripos($response, 'sent') !== false || stripos($response, '000') !== false) {
            error_log("SMS sent successfully to $phone");
            return true;
        } else {
            error_log("SMS sending failed to $phone. Response: $response");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("SMS sending exception for $phone: " . $e->getMessage());
        return false;
    }
}

try {
    // Check if consent is given
    if (!isset($_SESSION['consent_given']) || $_SESSION['consent_given'] !== true) {
        throw new Exception("Consent required");
    }

    if (empty($_POST['phone'])) {
        throw new Exception("Phone number is required");
    }

    $phone = $_POST['phone'];
    
    // Validate phone number
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        throw new Exception("Invalid phone number format");
    }

    $pdo = getDBConnection();
    
    // Check for existing valid OTP first
    $checkStmt = $pdo->prepare("SELECT otp_code FROM otp_verification 
                               WHERE phone = ? AND verified = 0 
                               AND expires_at > NOW() AND attempts < ? 
                               ORDER BY created_at DESC LIMIT 1");
    $checkStmt->execute([$phone, MAX_OTP_ATTEMPTS]);
    $existingOtp = $checkStmt->fetch();

    if ($existingOtp) {
        // Use existing OTP
        $otp = $existingOtp['otp_code'];
        error_log("Using existing OTP for $phone: $otp");
    } else {
        // Generate new OTP
        $otp = generateOTP();

        // Store OTP in database
        if (!storeOTP($phone, $otp, 'sms')) {
            throw new Exception("Failed to generate OTP");
        }
        error_log("Generated new OTP for $phone: $otp");
    }

    // Send SMS with the OTP
    if (!sendSMS($phone, $otp)) {
        throw new Exception("Failed to send SMS");
    }


    // Store phone in session for verification
    $_SESSION['otp_phone'] = $phone;

    echo json_encode([
        'success' => true,
        'message' => 'OTP sent successfully',
        'phone' => $phone
    ]);

} catch (Exception $e) {
    error_log("OTP sending error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>