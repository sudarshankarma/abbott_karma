<?php
require_once 'config.php';

header('Content-Type: application/json');

// SMS Configuration - Add these to your config.php
define('SMS_API_BASE_URL', 'http://cloud.smsindiahub.in/api');
define('SMS_USER', 'udit'); 
define('SMS_PASSWORD', 'Associate@123'); 
define('SMS_SENDER_ID', 'SMSHUB'); 
define('SMS_CHANNEL', 'Trans'); 
define('SMS_ROUTE', '##'); // Replace with your route ID if available
define('SMS_PEID', '1701158019630577568');

// function sendSMSWithAPIKey($phone, $message) {
//     try {
//         $formatted_number = '91' . $phone;
        
//         $params = [
//             'APIKey' => 'omUOKglyDEG8fl6lSFSE6w',
//             'senderid' => SMS_SENDER_ID,
//             'channel' => SMS_CHANNEL,
//             'DCS' => 0,
//             'flashsms' => 0,
//             'number' => $formatted_number,
//             'text' => urlencode($message),
//             'route' => SMS_ROUTE,
//             'PEId' => SMS_PEID
//         ];
        
//         // Build the API URL
//         $api_url = SMS_API_BASE_URL . '/mt/SendSMS?' . http_build_query($params);
        
//         // Send the request
//         $response = file_get_contents($api_url);
        
//         if ($response === FALSE) {
//             error_log("SMS API request failed for phone: $phone");
//             return false;
//         }
        
//         // Parse the response
//         $result = json_decode($response, true);
        
//         if (isset($result['ErrorCode']) && $result['ErrorCode'] === '000') {
//             error_log("SMS sent successfully to $phone. JobId: " . $result['JobId']);
//             return true;
//         } else {
//             $error_code = $result['ErrorCode'] ?? 'Unknown';
//             $error_message = $result['ErrorMessage'] ?? 'Unknown error';
//             error_log("SMS sending failed to $phone. Error: $error_code - $error_message");
//             return false;
//         }
        
//     } catch (Exception $e) {
//         error_log("SMS sending exception for $phone: " . $e->getMessage());
//         return false;
//     }
// }

function sendSMS($phone, $message) {
    $formattedNumber = '91' . $phone;

    $params = [
        'APIKey' => 'omUOKglyDEG8fl6lSFSE6w',
        'senderid' => SMS_SENDER_ID,
        'channel' => SMS_CHANNEL,
        'DCS' => 0,
        'flashsms' => 0,
        'number' => $formattedNumber,
        'text' => $message,
        // 'route' => SMS_ROUTE,
        'PEId' => SMS_PEID
    ];

    $apiUrl = SMS_API_BASE_URL . '/mt/SendSMS?' . http_build_query($params);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        error_log('SMS API request failed: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    $decoded = json_decode($response, true);
    print_r($decoded);
    exit;

    if (is_array($decoded)) {
        $errorCode = $decoded['ErrorCode'] ?? null;
        if ($errorCode === '000') {
            error_log("SMS sent successfully to $phone");
            return true;
        }
        $errorMessage = $decoded['ErrorMessage'] ?? 'Unknown error';
        error_log("SMS sending failed to $phone. Error: $errorCode - $errorMessage");
        return false;
    }

    // Fallback: treat plain string responses
    if (stripos($response, 'success') !== false) {
        error_log("SMS sent successfully to $phone. Response: $response");
        return true;
    }

    error_log("SMS sending failed to $phone. Raw response: $response");
    return false;
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

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://m3nk16.api.infobip.com/sms/3/messages',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS =>'{"messages":[{"sender":"InfoSMS","destinations":[{"to":"918898106409"}],"content":{"text":"'.$otp.'"}}]}',
        //     CURLOPT_HTTPHEADER => array(
        //         'Authorization: {authorization}',
        //         'Content-Type: application/json',
        //         'Accept: application/json'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;
        // Store OTP in database
        if (!storeOTP($phone, $otp, 'sms')) {
            throw new Exception("Failed to generate OTP");
        }
        error_log("Generated new OTP for $phone: $otp");
    }

    // Send SMS with the OTP
    $message = $otp;
    if (!sendSMS($phone, $message)) {
        throw new Exception("Failed to send SMS");
    }


    // Store phone in session for verification
    $_SESSION['otp_phone'] = $phone;

    echo json_encode([
        'success' => true,
        'message' => 'OTP sent successfully',
        'phone' => $phone,
        'otp' => $otp // For testing purposes, remove in production
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