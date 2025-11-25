<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['application_id']) || empty($_POST['document_type'])) {
        throw new Exception("Application ID and document type are required");
    }

    $applicationId = $_POST['application_id'];
    $documentType = $_POST['document_type'];

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM document_comments WHERE application_id = ? AND document_type = ? ORDER BY created_at ASC");
    $stmt->execute([$applicationId, $documentType]);
    $comments = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);

} catch (Exception $e) {
    error_log("Get comments error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>