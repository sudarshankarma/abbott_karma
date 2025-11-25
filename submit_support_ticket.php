<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['application_id']) || empty($_POST['subject']) || empty($_POST['message'])) {
        throw new Exception("All fields are required");
    }

    $applicationId = $_POST['application_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Insert support ticket
    $sql = "INSERT INTO support_tickets (application_id, subject, message, status) VALUES (?, ?, ?, 'open')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId, $subject, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Support ticket submitted successfully'
    ]);

} catch (Exception $e) {
    error_log("Support ticket error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>