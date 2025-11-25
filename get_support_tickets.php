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

    // Get tickets with unread message count
    $sql = "SELECT st.*, 
                   (SELECT COUNT(*) FROM support_messages sm 
                    WHERE sm.ticket_id = st.id AND sm.sender_type = 'admin' AND sm.is_read = 0) as unread_count
            FROM support_tickets st 
            WHERE st.application_id = ? 
            ORDER BY st.updated_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId]);
    $tickets = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'tickets' => $tickets
    ]);

} catch (Exception $e) {
    error_log("Get support tickets error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>