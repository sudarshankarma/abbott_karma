<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
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

    // Get total unread messages count
    $sql = "SELECT COUNT(*) as unread_count 
            FROM support_messages sm 
            JOIN support_tickets st ON sm.ticket_id = st.id 
            WHERE st.application_id = ? AND sm.sender_type = 'admin' AND sm.is_read = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId]);
    $result = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'unread_count' => $result['unread_count'] ?? 0
    ]);

} catch (Exception $e) {
    error_log("Get unread count error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>