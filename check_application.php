<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['phone'])) {
        throw new Exception("Phone number is required");
    }

    $phone = $_POST['phone'];
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE phone = ?");
    $stmt->execute([$phone]);
    $application = $stmt->fetch();

    if ($application) {
        echo json_encode([
            'success' => true,
            'application' => [
                'application_id' => $application['application_id'],
                'full_name' => $application['full_name'],
                'email' => $application['email'],
                'phone' => $application['phone'],
                'status' => $application['status'],
                'pan_card' => $application['pan_card'],
                'aadhar_card' => $application['aadhar_card'],
                'cancelled_cheque' => $application['cancelled_cheque']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No application found with this phone number'
        ]);
    }

} catch (Exception $e) {
    error_log("Application check error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>