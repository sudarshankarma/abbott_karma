<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['ticket_id']) || empty($_POST['message'])) {
        throw new Exception("Ticket ID and message are required");
    }

    $ticketId = $_POST['ticket_id'];
    $message = $_POST['message'];
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

    // Insert message
    $sql = "INSERT INTO support_messages (ticket_id, message, sender_type) VALUES (?, ?, 'user')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticketId, $message]);

    // Update ticket updated_at
    $updateStmt = $pdo->prepare("UPDATE support_tickets SET updated_at = NOW(), status = 'in_progress' WHERE id = ?");
    $updateStmt->execute([$ticketId]);

    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);

} catch (Exception $e) {
    error_log("Send support message error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>