<?php
/**
 * Database Configuration
 * Update these values according to your server setup
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'abbott_admin');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


// define('DB_HOST', 'localhost');
// define('DB_NAME', 'employee_registration');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_CHARSET', 'utf8mb4');

/**
 * Upload Configuration
 */
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);

/**
 * Application Configuration
 */
define('APP_NAME', 'Employee Registration System');
define('APP_URL', 'http://localhost/abbott/');

/**
 * OTP Configuration
 */
define('OTP_EXPIRY_MINUTES', 10);
define('MAX_OTP_ATTEMPTS', 3);
define('OTP_LENGTH', 6);

/**
 * Session Configuration
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Error Reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

/**
 * Timezone Configuration
 */
date_default_timezone_set('Asia/Kolkata');

/**
 * Database Connection Function
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

/**
 * File Upload Helper Function
 */
function uploadFile($file, $folder, $prefix = '') {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $targetPath = $folder . $newFileName;
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File too large (max 10MB)'];
        }
        
        // Validate file type
        if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Invalid file type. Only PDF, JPG, JPEG, PNG allowed'];
        }
        
        // Create directory if it doesn't exist
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0755, true)) {
                return ['success' => false, 'error' => 'Failed to create upload directory'];
            }
        }
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $newFileName];
        } else {
            return ['success' => false, 'error' => 'Failed to move uploaded file'];
        }
    } elseif ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file uploaded'];
    } else {
        return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
    }
}

/**
 * Generate Application ID
 */
function generateApplicationId() {
    $currentYear = date('Y');
    $randomNum = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return "EPF-{$currentYear}-{$randomNum}";
}

/**
 * Generate OTP Code
 */
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send SMS (Dummy Implementation)
 */
// function sendSMS($phone, $message) {
//     // In production, integrate with SMS provider like Twilio, MSG91, etc.
//     error_log("SMS to $phone: $message");
    
//     // Simulate SMS sending
//     return true;
// }

/**
 * Send WhatsApp (Dummy Implementation)
 */
function sendWhatsApp($phone, $message) {
    // In production, integrate with WhatsApp Business API
    error_log("WhatsApp to $phone: $message");
    
    // Simulate WhatsApp sending
    return true;
}

/**
 * Store OTP in Database
 */
function storeOTP($phone, $otp, $type = 'sms') {
    try {
        $pdo = getDBConnection();
        
        // Clean up expired OTPs
        $pdo->exec("DELETE FROM otp_verification WHERE expires_at < NOW()");
        
        $sql = "INSERT INTO otp_verification (phone, otp_code, verification_type) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$phone, $otp, $type]);
    } catch (Exception $e) {
        error_log("Error storing OTP: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify OTP
 */
function verifyOTP($phone, $otp) {
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT id FROM otp_verification 
                WHERE phone = ? AND otp_code = ? AND verified = 0 
                AND expires_at > NOW() AND attempts < ? 
                ORDER BY created_at DESC LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone, $otp, MAX_OTP_ATTEMPTS]);
        $otpRecord = $stmt->fetch();
        
        if ($otpRecord) {
            // Mark as verified
            $updateSql = "UPDATE otp_verification 
                         SET verified = 1, verified_at = NOW(), attempts = attempts + 1 
                         WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$otpRecord['id']]);
            
            return true;
        } else {
            // Increment attempts
            $updateSql = "UPDATE otp_verification 
                         SET attempts = attempts + 1 
                         WHERE phone = ? AND expires_at > NOW() 
                         ORDER BY created_at DESC LIMIT 1";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$phone]);
            
            return false;
        }
    } catch (Exception $e) {
        error_log("Error verifying OTP: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if phone is verified in application
 */
function isPhoneVerified($phone) {
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT phone_verified FROM applications WHERE phone = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone]);
        $application = $stmt->fetch();
        
        return $application && $application['phone_verified'] == 1;
    } catch (Exception $e) {
        error_log("Error checking phone verification: " . $e->getMessage());
        return false;
    }
}

/**
 * Get application status by phone
 */
function getApplicationStatus($phone) {
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT application_id, status, phone_verified FROM applications WHERE phone = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting application status: " . $e->getMessage());
        return null;
    }
}
?>