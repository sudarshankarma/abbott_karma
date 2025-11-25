<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['ticket_id'])) {
        throw new Exception("Ticket ID is required");
    }

    $ticketId = $_POST['ticket_id'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Verify the ticket belongs to the user
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT st.* FROM support_tickets st 
                               JOIN applications a ON st.application_id = a.application_id 
                               WHERE st.id = ? AND a.phone = ?");
    $checkStmt->execute([$ticketId, $verifiedPhone]);
    $ticket = $checkStmt->fetch();

    if (!$ticket) {
        throw new Exception("Ticket not found or access denied");
    }

    // Get messages for this ticket
    $stmt = $pdo->prepare("SELECT * FROM support_messages WHERE ticket_id = ? ORDER BY created_at ASC");
    $stmt->execute([$ticketId]);
    $messages = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);

} catch (Exception $e) {
    error_log("Get support messages error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>