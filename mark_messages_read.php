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

    // Mark admin messages as read
    $updateStmt = $pdo->prepare("UPDATE support_messages SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'admin'");
    $updateStmt->execute([$ticketId]);

    echo json_encode([
        'success' => true,
        'message' => 'Messages marked as read'
    ]);

} catch (Exception $e) {
    error_log("Mark messages read error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>