<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['verified_phone'])) {
        throw new Exception("Phone verification required");
    }

    if (empty($_POST['application_id']) || empty($_POST['document_type']) || empty($_POST['comment'])) {
        throw new Exception("All fields are required");
    }

    $applicationId = $_POST['application_id'];
    $documentType = $_POST['document_type'];
    $comment = $_POST['comment'];
    $verifiedPhone = $_SESSION['verified_phone'];

    // Verify application belongs to the verified phone
    $pdo = getDBConnection();
    $checkStmt = $pdo->prepare("SELECT application_id FROM applications WHERE application_id = ? AND phone = ?");
    $checkStmt->execute([$applicationId, $verifiedPhone]);
    $application = $checkStmt->fetch();

    if (!$application) {
        throw new Exception("Application not found or access denied");
    }

    // Insert comment
    $sql = "INSERT INTO document_comments (application_id, document_type, comment, commented_by) VALUES (?, ?, ?, 'user')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId, $documentType, $comment]);

    echo json_encode([
        'success' => true,
        'message' => 'Comment submitted successfully'
    ]);

} catch (Exception $e) {
    error_log("Comment submission error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>